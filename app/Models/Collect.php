<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collect extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Collect $collect) {
            if (empty($collect->token)) {
                do {
                    $token = (string) random_int(10000, 99999);
                } while (self::where('token', $token)->exists());

                $collect->token = $token;
            }
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'place_id',
        'day',
        'start_time',
        'end_time',
        'link_appointment',
        'token',
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
