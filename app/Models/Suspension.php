<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suspension extends Model
{
    public $timestamps  = false;

    protected $table = 'suspension';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function admin() {
        return $this->belongsTo(Admin::class);
    }
}
