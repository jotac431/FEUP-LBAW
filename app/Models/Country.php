<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    private const flagOffset = 0x1F1E6;
    private const asciiOffset = 0x41;

    public $timestamps  = false;

    protected $table = 'country';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name',
    ];

    public function citizens() {
        return $this->hasMany(User::class);
    }

    public function getInfo() {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'flag' => $this->getUtf8Emoji()
        ];
    }

    public static function getIdByName($name) {
        return Country::where('name', $name)->first()->id;
    }

    /**
     * The UTF-8 country flag is made of 2 unicode characters
     * which are encoded separately
     */
    public function getUtf8Emoji() {
        $unicodeFirst = ord($this->code[0]) - $this::asciiOffset + $this::flagOffset;
        $unicodeSecond = ord($this->code[1]) - $this::asciiOffset + $this::flagOffset;

        return [$unicodeFirst, $unicodeSecond];
    }
}
