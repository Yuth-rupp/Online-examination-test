<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/**
 * 1. Default Private User Channel
 * Used for targeted system notifications to specific users.
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // NOTE: this app's User primary key is `user_id`, not `id` — fixed below.
    return (int) $user->user_id === (int) $id;
});

/**
 * 1b. Real-Time Notification Channel
 * Powers the notification bell on Dashboard, History, Exams, Settings
 * and Support. Each user only gets access to their own notification feed.
 */
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->user_id === (int) $userId;
});

/**
 * 2. Secure Exam Presence Channel
 * This allows the teacher monitoring dashboard to see who is online right now.
 * It counts active students and handles the "No Students Live Right Now" view.
 */
Broadcast::channel('exam.{examId}', function ($user, $examId) {
    if ($user->role === 'student' || $user->role === 'teacher') {
        return [
            'id'   => $user->user_id ?? $user->id,
            'name' => $user->full_name,
            'role' => $user->role
        ];
    }
    
    return false;
});

/**
 * 3. Private Proctor Video Stream Channel
 * This is the private pipe where the student's base64 image strings travel 
 * safely directly onto the examiner's dashboard grid layout.
 */
Broadcast::channel('exam.stream.{examId}', function ($user, $examId) {
    return $user->role === 'student' || $user->role === 'teacher';
});

/**
 * =========================================================================
 * ⚡ ADDED: PROCTOR AUTHORIZATION HANDSHAKE CHANNELS
 * =========================================================================
 */

/**
 * 4. Exam Room Handshake Channel (Public/Global Channel alternative syntax)
 * Opens authorization listener access so the teacher dashboard can collect 
 * the randomized handshake codes generated on student machines.
 */
Broadcast::channel('exam-room-handshake', function () {
    return true; 
});

/**
 * 5. Student Proctor Authorization Channel
 * Allows individual student machines to listen for the specific event signaling 
 * that the teacher clicked "Admit Student Feed" for their unique key.
 */
Broadcast::channel('student-proctor-auth.{proctor_key}', function ($user, $proctor_key) {
    return true; 
});

/**
 * 6. Global Exam Monitoring Stream Channel
 * The broadcast pipeline over which approved student webcam frames travel 
 * to render on the proctor grid cards.
 */
Broadcast::channel('exam-monitoring', function () {
    return true; 
});