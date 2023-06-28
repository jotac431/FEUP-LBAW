<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Content
{
  protected $table = 'comment';

  protected $primaryKey = 'content_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public static function boot()
    {
        parent::boot();

        // All the queries are joined with the content table
        static::addGlobalScope(function ($query) {
            $query->join('content', 'content_id', '=', 'id');
        });
    }

    public function article() {
      return $this->belongsTo(Article::class, 'article_id');
    }

    public function content() {
      return $this->belongsTo(Content::class);
    }

    public function parent_comment() {
      return $this->belongsTo(Comment::class, 'parent_comment_id');
    } 

    public function child_comments() {
      return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    public function notification() {
      return $this->hasOne(CommentNotification::class, 'new_comment');
    }

    public function getInfo() {
      $published_at = date('F j, Y', strtotime( $this['published_at'] ) ) ;
      $isAuthor = isset($this->author) ? $this->author->id === Auth::id() : false;

      $feedback = Auth::check()
        ? Auth::user()->feedback->where('content_id', '=', $this['id'])->first()
        : null;

      $liked = false;
      $disliked = false;

      if (!is_null($feedback)) {
        $liked = $feedback['is_like'];
        $disliked = !$feedback['is_like'];
      }

      return [
          'id' => $this->content_id, 
          'body' => $this->body,
          'likes' => $this->likes,
          'dislikes' => $this->dislikes,
          'published_at' => $published_at,
          'article_id' => $this->article_id,
          'is_edited' => $this->is_edited,
          'liked' => $liked,
          'disliked' => $disliked,
          'isAuthor' => $isAuthor,
          'hasFeedback' => $this['likes'] != 0 || $this['dislikes'] != 0,
          'author' => isset($this->author) ? [
              'id' => $this->author->id,
              'name' => $this->author->name,
              'avatar' => $this->author->avatar,
          ] : null,
      ];
    }
}
