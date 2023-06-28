<?php

namespace App\Listeners;

use App\Events\ArticleLike;
use App\Events\Comment;
use App\Events\CommentLike;
use App\Events\CommentReply;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotification implements ShouldQueue
{
    
}
