<?php

namespace App\Events;

use App\Models\Discussion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Discussion $discussion;

    public function __construct(Discussion $discussion)
    {
        $this->discussion = $discussion->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('course.' . $this->discussion->course_id);
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->discussion->id,
            'content' => $this->discussion->content,
            'user' => [
                'id' => $this->discussion->user->id,
                'name' => $this->discussion->user->name
            ],
            'created_at' => $this->discussion->created_at->toDateTimeString()
        ];
    }

    public function broadcastAs(): string
    {
        return 'discussion.created';
    }
}
