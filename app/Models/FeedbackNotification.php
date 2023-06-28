<?php

namespace App\Models;

use App\Events\ArticleLike;
use App\Events\CommentLike;

class FeedbackNotification extends Notification
{

    public static function boot()
    {
        parent::boot();

        // All the queries are the same as User but only for admins
        static::addGlobalScope(function ($query) {
            $query->where('type', 'FEEDBACK');
        });
    }

    public static function notify($user, $content, $isArticle)
    {
        if (!isset($content->author_id)) return;

        if ($isArticle)
        {
            $title = Article::find($content->id)->title;
            event(new ArticleLike(
                $content->author_id, $user->name, asset('storage/avatars/'.$user['avatar']),
                $user->id, $content->id, $title
            ));
        }
        else
        {
            $article = Comment::find($content->id)->article;
            event(new CommentLike(
                $content->author_id, $user->name, asset('storage/avatars/'.$user['avatar']),
                $user->id, $article->id, $content->body, $article->title
            ));
        }
    }

    public function feedback_giver() {
        return $this->belongsTo(User::class, 'fb_giver');
    }

    public function content() {
        return $this->belongsTo(Content::class, 'rated_content');
    }
}
