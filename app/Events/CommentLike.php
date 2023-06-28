<?php

namespace App\Events;

class CommentLike extends Notification
{
    public $article_id;
    public $comment_body;
    public $article_title;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver_id, $username, $avatar, $user_id, $article_id, $comment_body, $article_title)
    {
        parent::__construct($receiver_id, $username, $avatar, $user_id);
        $this->article_id = $article_id;
        $this->comment_body = $comment_body;
        $this->article_title = $article_title;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'comment-like';
    }
}
