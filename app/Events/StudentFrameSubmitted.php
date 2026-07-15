<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentFrameSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exam_id;
    public $image_frame;
    public $proctor_key;
    public $student_id;

    public function __construct(array $data)
    {
        $this->exam_id = $data['exam_id'] ?? null;
        $this->image_frame = $data['image_frame'] ?? null;
        $this->proctor_key = $data['proctor_key'] ?? null;
        $this->student_id = $data['student_id'] ?? '2';
    }

    public function broadcastOn()
    {
        return new Channel('exam-monitoring');
    }

    public function broadcastAs()
    {
        return 'StudentFrameSubmitted';
    }
}