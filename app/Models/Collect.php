<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

            // Le token est désormais garanti : on peut composer le slug d'URL.
            $collect->slug = $collect->generateSlug();
        });

        // Si la date ou le lieu changent, l'URL lisible doit suivre (le token reste stable).
        static::updating(function (Collect $collect) {
            if ($collect->isDirty(['day', 'place_id'])) {
                $collect->slug = $collect->generateSlug();
            }
        });
    }

    /**
     * Compose le slug d'URL de la collecte : « date - ville - token ».
     * Le suffixe token garantit l'unicité (même jour + même ville) et la non-devinabilité.
     */
    public function generateSlug(): string
    {
        $city = Place::find($this->place_id)?->city
            ?? Place::find($this->place_id)?->name
            ?? '';

        $day = $this->day instanceof \DateTimeInterface
            ? $this->day->format('Y-m-d')
            : (string) $this->day;

        return Str::slug(trim($day.' '.$city)).'-'.$this->token;
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
        'slug',
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
