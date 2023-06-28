<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentNotification;
use App\Models\FeedbackNotification;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Returns a list of the user's notifications
     * 
     * @return View
     */
    public function show()
    {
        $this->authorize('notifications', User::class);

        $notifications = Auth::user()->notifications->sortByDesc('date')
            ->whereIn('type', ['FEEDBACK', 'COMMENT'])
            ->map(function ($notification) {
                $now = new DateTime('now');
                $notifDate = new DateTime($notification->date);
                $interval = $now->diff($notifDate);
                $timeDiff = $this->formatTimeDiff($interval);

                $info = [
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'time' => $timeDiff,
                ];

                if ($notification->type === "COMMENT")
                {
                    $notification = CommentNotification::find($notification->id);

                    if (isset($notification->comment->parent_comment_id)) {
                        $info['type'] = 'REPLY';
                        $info['header'] = "New reply to one of your comments";
                    } else {
                        $info['header'] = "New comment in one of your articles";
                    }

                    $info['username'] = $notification->comment->author->name;
                    $info['avatar'] = $notification->comment->author->avatar;
                    $info['user_id'] = $notification->comment->author->id;
                    $info['article_id'] = $notification->comment->article->id;
                    $info['article_title'] = $notification->comment->article->title;
                    $info['comment_body'] = mb_strimwidth($notification->comment->body, 0, 103, "...");
                } else if ($notification->type === "FEEDBACK")
                {
                    $notification = FeedbackNotification::find($notification->id);

                    $info['username'] = $notification->feedback_giver->name;
                    $info['avatar'] = $notification->content->author->avatar;
                    $info['user_id'] = $notification->fb_giver;

                    $content = Article::find($notification->rated_content);
                    if (isset($content))
                    {
                        $info['type'] = 'ARTICLE_LIKE';
                        $info['article_id'] = $content->id;
                        $info['article_title'] = $content->title;
                        $info['header'] = "New like in one of your articles";
                    } else
                    {
                        $content = Comment::find($notification->rated_content);
                        $info['type'] = 'COMMENT_LIKE';
                        $info['article_id'] = $content->article_id;
                        $info['article_title'] = $content->article->title;
                        $info['comment_body'] = mb_strimwidth($content->body, 0, 103, "...");
                        $info['header'] = "New like in one of your comments";
                    }
                }

                return $info;
            });

        return view('partials.notifications', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Marks all user's notifications as read
     * 
     * @return Response
     */
    public function readNotifications()
    {
        $this->authorize('notifications', User::class);

        $notifications = Auth::user()->notifications;
        $notifications->each(function ($notification) {
            $notification->is_read = true;
            $notification->save();
        });

        return response()->json([
            'status' => 'OK',
            'msg' => 'Successfully marked notifications as read'
        ], 200);
    }

    private function formatTimeDiff($diff)
    {
        $res = $diff->format('%y years ago');
        if ($res[0] === '1')
            $res = $diff->format('%y year ago');
        if ($res[0] > '0') return $res;

        $res = $diff->format('%m months ago');
        if ($res[0] === '1')
            $res = $diff->format('%m month ago');
        if ($res[0] > '0') return $res;

        $res = $diff->format('%d days ago');
        if ($res[0] === '1')
            $res = $diff->format('%d day ago');
        if ($res[0] > '0') return $res;

        $res = $diff->format('%h hours ago');
        if ($res[0] === '1')
            $res = $diff->format('%h hour ago');
        if ($res[0] > '0') return $res;

        $res = $diff->format('%i minutes ago');
        if ($res[0] === '1')
            $res = $diff->format('%i minute ago');
        if ($res[0] > '0') return $res;

        $res = $diff->format('%s seconds ago');
        if ($res[0] === '1')
            $res = $diff->format('%s second ago');
        if ($res[0] > '0') return $res;

        return "just now";
    }
}
