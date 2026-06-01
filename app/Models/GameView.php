<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameView extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'message',
        'descr',
        'text',
        'button_text',
        'button_url',
        'integration_url',
    ];

    /**
     * @return HasMany<GameChoice>
     */
    public function choices(): HasMany
    {
        return $this->hasMany(GameChoice::class, 'view_id');
    }
}
