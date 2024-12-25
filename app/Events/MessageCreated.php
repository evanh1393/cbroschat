<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel; // Use PrivateChannel for authenticated access
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message; // will hold the array from Redis

    public function __construct(array $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('cbros-chat-111'); // Updated channel name
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
