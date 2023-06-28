<?php

namespace App\Models;

use App\Events\Comment as CommentEvent;
use App\Events\CommentReply;

class CommentNotification extends Notification
{
    public static function boot()
    {
        parent::boot();

        // All the queries are the same as User but only for admins
        static::addGlobalScope(function ($query) {
            $query->where('type', 'COMMENT');
        });
    }

    public static function notify($receiver_id, $comment, $user, $article)
    {
        if (!isset($receiver_id) || $receiver_id == $user['id'])
            return;

        if (isset($comment['parent_comment_id']))
        {
            event(new CommentReply(
                $receiver_id, $user['name'], asset('storage/avatars/'.$user['avatar'])
                , $user->id, $article['id'], $article['title'], $comment['body']
            ));
        }
        else
        {
            event(new CommentEvent(
                $receiver_id, $user['name'], asset('storage/avatars/'.$user['avatar'])
                , $user->id, $article['id'], $article['title'], $comment['body']
            ));
        }
    }

    public function comment() {
      return $this->belongsTo(Comment::class, 'new_comment');
    }
}
