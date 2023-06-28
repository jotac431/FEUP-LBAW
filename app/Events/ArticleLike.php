<?php

namespace App\Events;

class ArticleLike extends Notification
{
    public $article_id;
    public $article_title;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver_id, $username, $avatar, $user_id, $article_id, $article_title)
    {
        parent::__construct($receiver_id, $username, $avatar, $user_id);
        $this->article_id = $article_id;
        $this->article_title = $article_title;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'article-like';
    }
}
