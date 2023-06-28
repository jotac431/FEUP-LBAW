<?php

namespace App\Events;

class CommentReply extends Notification
{
    public $article_id;
    public $article_title;
    public $comment_body;
    // TODO: How to see the reply after clicking in the notification

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver_id, $username, $avatar, $user_id, $article_id, $article_title, $comment_body)
    {
        parent::__construct($receiver_id, $username, $avatar, $user_id);
        $this->article_id = $article_id;
        $this->article_title = $article_title;
        $this->comment_body = $comment_body;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'comment-reply';
    }
}
