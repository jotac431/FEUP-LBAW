<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class Notification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $receiver_id;
    public $username;
    public $avatar;
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver_id, $username, $avatar, $user_id)
    {
        $this->receiver_id = $receiver_id;
        $this->username = $username;
        $this->avatar = $avatar;
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['notifications.'.$this->receiver_id];
    }

    abstract public function broadcastAs();
}
