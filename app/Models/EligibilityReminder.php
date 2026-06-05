<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EligibilityReminder extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'locale',
        'collect_id',
        'eligible_at',
        'sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'eligible_at' => 'date',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Collect, self>
     */
    public function collect(): BelongsTo
    {
        return $this->belongsTo(Collect::class);
    }
}
