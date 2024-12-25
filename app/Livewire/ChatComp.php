<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Redis;
use App\Events\MessageCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ChatComp extends Component
{
    public ?string $chatMessage = '';
    public array $messages = [];

    protected $listeners = ['redisNewMessage'];

    public function mount()
    {
        $this->authorizeAccess();
        $this->loadMessagesFromRedis();
    }

    private function authorizeAccess()
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
    }

    public function loadMessagesFromRedis()
    {
        $rawMessages = Redis::lrange('cbros-chat-111:messages', 0, -1); // Updated Redis key
        $this->messages = array_map(function ($item) {
            return json_decode($item, true);
        }, $rawMessages);
    }

    public function sendMessage()
    {
        $this->validate([
            'chatMessage' => 'required|string|max:500',
        ]);

        $userId = Auth::id();
        $key = 'send-message:' . $userId;

        // Rate Limiting: Max 5 messages per minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'chatMessage' => 'You are sending messages too quickly. Please wait a moment.',
            ]);
        }

        RateLimiter::hit($key, 60); // Decay time: 60 seconds

        $cleanBody = $this->sanitizeMessage($this->chatMessage);

        $message = [
            'id'      => uniqid(),
            'body'    => $cleanBody,
            'user'    => Auth::user()->name ?? 'Guest',
            'created' => now()->toDateTimeString(),
        ];

        Redis::rpush('cbros-chat-111:messages', json_encode($message)); // Updated Redis key
        Redis::ltrim('cbros-chat-111:messages', -200, -1); // Optional: Limit to last 200 messages

        broadcast(new MessageCreated($message))->toOthers(); // Broadcast to 'cbros-chat-111'

        $this->chatMessage = '';
        $this->messages[] = $message;
    }

    private function sanitizeMessage($message)
    {
        // Trim whitespace
        $message = Str::trim($message);

        // Strip all HTML tags except for allowed ones
        $allowedTags = '<b><i><strong><em><u>';
        $message = strip_tags($message, $allowedTags);

        return $message;
    }

    public function redisNewMessage($message)
    {
        $this->messages[] = $message;
    }

    public function render()
    {
        return view('livewire.chat-comp')
            ->layout('layouts.app');
    }
}
