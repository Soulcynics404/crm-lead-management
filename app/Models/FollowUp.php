<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'follow_up_date',
        'follow_up_time',
        'notes',
        'status',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    /**
     * Get the lead that this follow-up belongs to.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
