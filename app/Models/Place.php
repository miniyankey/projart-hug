<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'locality',
        'city',
        'room',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'locality' => 'integer',
        ];
    }
}
