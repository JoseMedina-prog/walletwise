<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id', 'transaction_id', 'amount', 'contribution_date', 'note',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'contribution_date' => 'date',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}