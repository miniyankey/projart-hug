<?php

namespace App\Models;

use App\Enums\FormSubmissionType;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'company_name',
        'name',
        'contact_email',
        'message',
        'trophy_participation',
        'preferred_dates',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => FormSubmissionType::class,
            'trophy_participation' => 'boolean',
            'preferred_dates' => 'array',
        ];
    }
}
