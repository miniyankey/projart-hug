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
        'company_address',
        'company_city',
        'company_postal_code',
        'firstname',
        'lastname',
        'contact_email',
        'message',
        'preferred_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => FormSubmissionType::class,
            'preferred_date' => 'date',
        ];
    }
}
