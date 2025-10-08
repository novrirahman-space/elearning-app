<?php

namespace App\Events;

use App\Models\Reply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reply $reply;

    public function __construct(Reply $reply)
    {
        $this->reply = $reply->load('user', 'discussion');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('course.' . $this->reply->discussion->course_id);
    }

    public function broadcastAs(): string
    {
        return 'reply.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reply->id,
            'content' => $this->reply->content,
            'user' => [
                'id' => $this->reply->user->id,
                'name' => $this->reply->user->name
            ],
            'discussion_id' => $this->reply->discussion_id,
            'created_at' => $this->reply->created_at->toDateTimeString()
        ];
    }
}
