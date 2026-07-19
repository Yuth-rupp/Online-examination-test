<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Display the teacher settings blade view.
     * Accessible via: GET /teacher/settings
     */
    public function index()
    {
        return view('teacher.settings');
    }

    /**
     * Store incoming avatar imagery using AJAX payload criteria.
     * Accessible via: POST /teacher/settings/avatar
     */
    public function updateAvatar(Request $request)
    {
        // 1. Validate incoming file input
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        // 2. Clear out older profile imagery references from storage disk if existing
        if ($user->profile && $user->profile->avatar) {
            $normalizedPath = str_replace('/storage/', 'public/', $user->profile->avatar);
            if (Storage::exists($normalizedPath)) {
                Storage::delete($normalizedPath);
            }
        }

        // 3. Process file upload execution mapping onto public symlink structure
        if ($request->hasFile('avatar')) {
            $filePath = $request->file('avatar')->store('public/avatars');
            $publicUrl = Storage::url($filePath);

            // 4. Update the profile instance matching database conditions matching schema
            $user->profile()->updateOrCreate(
                ['user_id' => $user->user_id],
                ['avatar' => $publicUrl]
            );

            // 5. Send structural response back to front-end handler matching your JS logic 
            return response()->json([
                'success' => true,
                'avatar_url' => $publicUrl
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image processing aborted unexpectedly.'
        ], 400);
    }
}