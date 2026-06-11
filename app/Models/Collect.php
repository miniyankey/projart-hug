<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Collect extends Model
{
    /**
     * Une collecte reste « en cours » jusqu'à ce nombre de jours après son jour J,
     * délai au-delà duquel elle bascule « terminée » (lien co-brandé fermé).
     */
    private const ONGOING_GRACE_DAYS = 7;

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
    ];

    /**
     * Attributs dérivés de la date, exposés au front (pas de colonne en base).
     *
     * @var list<string>
     */
    protected $appends = ['status', 'is_past'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day' => 'date:Y-m-d',
        ];
    }

    /**
     * Jour limite (inclus) jusqu'auquel une collecte est encore « en cours ».
     */
    private static function ongoingThreshold(): Carbon
    {
        return Carbon::today()->subDays(self::ONGOING_GRACE_DAYS);
    }

    /**
     * Une collecte est « en cours » du moment où son jour J n'est pas dépassé
     * de plus d'une semaine (jour futur, jour J, ou jusqu'à 7 jours après).
     */
    public function isOngoing(): bool
    {
        return $this->day !== null && $this->day->gte(self::ongoingThreshold());
    }

    /**
     * Statut public de la collecte, dérivé de sa date.
     *
     * @return 'ongoing'|'ended'
     */
    public function getStatusAttribute(): string
    {
        return $this->isOngoing() ? 'ongoing' : 'ended';
    }

    /**
     * Le jour J est-il dépassé ? (le jour même reste « non passé »). Sert à
     * basculer la page collecte en mode « terminée » pendant la semaine de
     * grâce, où le lien reste accessible mais l'inscription n'a plus de sens.
     */
    public function getIsPastAttribute(): bool
    {
        return $this->day !== null && $this->day->lt(Carbon::today());
    }

    /**
     * Restreint la requête aux collectes encore en cours (lien co-brandé ouvert).
     */
    public function scopeOngoing(Builder $query): Builder
    {
        return $query->whereDate('day', '>=', self::ongoingThreshold());
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
