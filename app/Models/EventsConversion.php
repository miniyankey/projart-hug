<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsConversion extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'appointment_click',
        'source',
        'collect_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_click' => 'boolean',
        ];
    }

    public function collect()
    {
        return $this->belongsTo(Collect::class);
    }
}
