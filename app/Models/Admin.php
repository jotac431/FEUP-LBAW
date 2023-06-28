<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends User
{
    public static function boot()
    {
        parent::boot();

        // All the queries are the same as User but only for admins
        static::addGlobalScope(function ($query) {
            $query->where('is_admin', true);
        });
    }

    public function givenSuspensions() {
        return $this->hasMany(Suspension::class, 'admin_id');
    }
}
