<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameChoice extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'question_id',
        'view_id',
        'text',
        'descr',
        'eligible',
        'ineligibility_days',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'eligible' => 'boolean',
            'ineligibility_days' => 'integer',
            'order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<GameQuestion, self>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(GameQuestion::class, 'question_id');
    }

    /**
     * @return BelongsTo<GameView, self>
     */
    public function view(): BelongsTo
    {
        return $this->belongsTo(GameView::class, 'view_id');
    }
}
