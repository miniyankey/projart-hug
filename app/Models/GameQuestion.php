<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameQuestion extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'titre',
        'question',
        'type',
        'why_question',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'order' => 'integer',
        ];
    }

    /**
     * @return HasMany<GameChoice>
     */
    public function choices(): HasMany
    {
        return $this->hasMany(GameChoice::class, 'question_id')->orderBy('order');
    }
}
