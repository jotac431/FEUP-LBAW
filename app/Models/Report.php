<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps  = false;

    protected $table = 'report';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'is_closed',
    ];

    function reported() {
        return $this->belongsTo(User::class);
    }

    function reporter() {
        return $this->belongsTo(User::class);
    }
}
