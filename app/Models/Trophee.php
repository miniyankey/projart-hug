<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trophee extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'year_of',
        'rank',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year_of' => 'integer',
            'rank' => 'integer',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
