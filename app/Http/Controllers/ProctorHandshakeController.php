<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Events\ProctorKeyRegistered;
use App\Events\ProctorKeyApproved;
use App\Events\StudentFrameSubmitted;

class ProctorHandshakeController extends Controller
{
    /**
     * Shared cache key that holds the full list of pending proctor keys.
     * This lets the teacher monitoring page poll via HTTP even if the
     * real-time WebSocket event was already fired before the page opened.
     */
    private const PENDING_LIST_KEY = 'proctor-pending-list';

    // =========================================================================
    // STUDENT: registers their camera key when they click "Turn On"
    // Route: POST /student/exams/register-proctor-key
    // =========================================================================
    public function registerKey(Request $request)
    {
        $validated = $request->validate([
            'proctor_key'  => 'required|string',
            'student_id'   => 'required',
            'student_name' => 'required|string',
            'exam_id'      => 'required',
        ]);

        // 1. Store individual key status (used by streamProctorFrame authorization check)
        Cache::put('proctor-status-' . $validated['proctor_key'], 'pending', 1800);

        // 2. ✅ FIX: Store full student data in a shared list so the teacher monitoring
        //    page can retrieve it via HTTP polling — even if the teacher opens the page
        //    AFTER this event already fired and was missed via WebSocket.
        $pendingList = Cache::get(self::PENDING_LIST_KEY, []);
        $pendingList[$validated['proctor_key']] = [
            'proctor_key'   => $validated['proctor_key'],
            'student_id'    => $validated['student_id'],
            'student_name'  => $validated['student_name'],
            'exam_id'       => $validated['exam_id'],
            'registered_at' => now()->toIso8601String(),
        ];
        Cache::put(self::PENDING_LIST_KEY, $pendingList, 1800);

        // 3. Broadcast real-time WebSocket event to the teacher (works if teacher
        //    page is already open when student clicks Turn On)
        ProctorKeyRegistered::dispatch([
            'exam_id'      => $validated['exam_id'],
            'proctor_key'  => $validated['proctor_key'],
            'student_id'   => $validated['student_id'],
            'student_name' => $validated['student_name'],
        ]);

        return response()->json(['status' => 'key_registered_awaiting_teacher']);
    }

    // =========================================================================
    // ✅ NEW METHOD — TEACHER: HTTP polling endpoint
    // Called by the teacher monitoring page every 5 seconds.
    // Returns all currently-pending proctor keys from the cache list.
    // Route: GET /teacher/monitoring/pending-keys
    // =========================================================================
    public function getPendingKeys(Request $request)
    {
        $pendingList = Cache::get(self::PENDING_LIST_KEY, []);

        // Only return keys whose status is still 'pending' (not yet approved/denied)
        $filtered = array_filter($pendingList, function ($item) {
            $status = Cache::get('proctor-status-' . $item['proctor_key']);
            return $status === 'pending';
        });

        return response()->json(array_values($filtered));
    }

    // =========================================================================
    // TEACHER: approves a student key when they click "Admit Student"
    // Route: POST /teacher/monitoring/approve-proctor-key
    // =========================================================================
    public function approveKey(Request $request)
    {
        $validated = $request->validate([
            'proctor_key'  => 'required|string',
            'student_id'   => 'required',
            'student_name' => 'required|string',
        ]);

        // 1. Mark as approved so student can start streaming frames
        Cache::put('proctor-status-' . $validated['proctor_key'], 'approved', 1800);

        // 2. ✅ FIX: Remove from pending list so it stops showing up in polling results
        $pendingList = Cache::get(self::PENDING_LIST_KEY, []);
        unset($pendingList[$validated['proctor_key']]);
        Cache::put(self::PENDING_LIST_KEY, $pendingList, 1800);

        // 3. Broadcast approval so the student's page updates from
        //    "Awaiting Verification" → "Verified / Streaming"
        ProctorKeyApproved::dispatch([
            'proctor_key'  => $validated['proctor_key'],
            'student_id'   => $validated['student_id'],
            'student_name' => $validated['student_name'],
        ]);

        return response()->json(['status' => 'student_feed_admitted_successfully']);
    }

    // =========================================================================
    // STUDENT: streams webcam frames to the teacher's monitoring grid
    // Route: POST /student/exams/stream-frame
    // =========================================================================
    public function streamProctorFrame(Request $request)
    {
        $validated = $request->validate([
            'proctor_key' => 'required|string',
            'student_id'  => 'required',
            'image_frame' => 'required|string',
            'exam_id'     => 'required',
        ]);

        // Security gate: only stream if teacher has already approved this key
        if (Cache::get('proctor-status-' . $validated['proctor_key']) !== 'approved') {
            return response()->json(['error' => 'Stream unauthorized — key not yet approved.'], 403);
        }

        StudentFrameSubmitted::dispatch([
            'exam_id'     => $validated['exam_id'],
            'image_frame' => $validated['image_frame'],
            'proctor_key' => $validated['proctor_key'],
            'student_id'  => $validated['student_id'],
        ]);

        return response()->json(['status' => 'frame_synced']);
    }
}