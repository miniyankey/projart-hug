<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactFormConversion extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'form_click',
        'form_sent',
        'trophee_participation',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'form_click' => 'boolean',
            'form_sent' => 'boolean',
            'trophee_participation' => 'boolean',
        ];
    }
}
