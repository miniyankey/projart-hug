<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsEligibiliteStep extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'collect_id',
        'step',
        'result',
        'completed',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'step' => 'integer',
            'completed' => 'boolean',
        ];
    }

    public function collect()
    {
        return $this->belongsTo(Collect::class);
    }
}
