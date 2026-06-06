<?php

namespace App\Models;

use App\Enums\FormSubmissionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'handled_at',
        'handled_by',
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
            'handled_at' => 'datetime',
        ];
    }

    /**
     * The admin who marked this submission as already contacted.
     *
     * @return BelongsTo<Admin, $this>
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'handled_by');
    }
}
