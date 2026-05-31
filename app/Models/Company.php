<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property string|null $logo Chemin relatif au disque `public`, ex: `logos/{slug}.png`.
 *                             Accessible via Storage::disk('public')->url($company->logo).
 * @property string|null $logo_url URL publique du logo (null si absent).
 * @property string $token Token permanent alimentant le lien co-brandé /{slug}/{token}/collecte.
 */
class Company extends Model
{
    /**
     * @var list<string>
     */
    protected $appends = ['logo_url'];

    protected static function booted(): void
    {
        static::creating(function (Company $company): void {
            if (empty($company->token)) {
                do {
                    $token = Str::lower(Str::random(8));
                } while (self::where('token', $token)->exists());

                $company->token = $token;
            }
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'token',
        'logo',
        'color',
        'color_secondary',
        'email_contact',
        'contact_name',
        'contact_phone',
        'street',
        'postal_code',
        'city',
        'country',
        'is_labelled',
        'labelled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_labelled' => 'boolean',
            'labelled_at' => 'datetime',
        ];
    }

    /**
     * URL publique du logo, ou null s'il n'y en a pas.
     *
     * @return Attribute<string|null, never>
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->logo
                ? Storage::disk('public')->url($this->logo)
                : null,
        );
    }

    public function collects()
    {
        return $this->hasMany(Collect::class);
    }

    public function trophees()
    {
        return $this->hasMany(Trophee::class);
    }
}
