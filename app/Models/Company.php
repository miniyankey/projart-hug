<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $logo Chemin relatif au disque `public`, ex: `logos/{slug}.png`.
 *                             Accessible via Storage::disk('public')->url($company->logo).
 */
class Company extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'email_contact',
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

    public function collects()
    {
        return $this->hasMany(Collect::class);
    }
}
