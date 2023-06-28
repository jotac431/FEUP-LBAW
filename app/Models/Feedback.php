<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public $timestamps  = false;

    protected $table = 'feedback';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'content_id', 'is_like'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function content() {
      return $this->belongsTo(Content::class);
    }
}
