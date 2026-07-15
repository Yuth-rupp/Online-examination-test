<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamStreamEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $examId;
    public $studentId;
    public $studentName;
    public $imageFrame;

    /**
     * Create a new event instance.
     *
     * @param string $examId
     * @param int|string $studentId
     * @param string $studentName
     * @param string $imageFrame
     */
    public function __construct($examId, $studentId, $studentName, $imageFrame)
    {
        $this->examId = $examId;
        $this->studentId = $studentId;
        $this->studentName = $studentName;
        $this->imageFrame = $imageFrame; // The base64 text string of the image
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This matches the private channel route we added to channels.php
        return [
            new PrivateChannel('exam.stream.' . $this->examId),
        ];
    }

    /**
     * The event's broadcast name.
     * This makes it easy to listen to on the frontend JavaScript layer.
     */
    public function broadcastAs(): string
    {
        return 'StreamFrameUpdated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'student_id'   => $this->studentId,
            'student_name' => $this->studentName,
            'image_frame'  => $this->imageFrame,
            'timestamp'    => now()->toIso8601String(),
        ];
    }
}