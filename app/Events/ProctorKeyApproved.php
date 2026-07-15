<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProctorKeyApproved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $proctor_key;
    public $student_id;
    public $student_name;

    public function __construct(array $data)
    {
        $this->proctor_key = $data['proctor_key'] ?? null;
        $this->student_id = $data['student_id'] ?? null;
        $this->student_name = $data['student_name'] ?? 'Student';
    }

    public function broadcastOn()
    {
        return new Channel('exam-room-handshake');
    }

    public function broadcastAs()
    {
        return 'ProctorKeyApproved';
    }
}