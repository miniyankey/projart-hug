<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collect extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'place_id',
        'day',
        'link_appointment',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'day' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function eligibiliteSteps()
    {
        return $this->hasMany(EventsEligibiliteStep::class);
    }
    public function eventsConversions()
    {
        return $this->hasMany(EventsConversion::class);
    }
}
