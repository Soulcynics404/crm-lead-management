<?php

/**
 * HK CRM Lead Management System
 *
 * @author    Harsshh (@Soulcynics404)
 * @github    https://github.com/Soulcynics404/crm-lead-management
 * @quote     "Breaking systems to make them secure."
 * @copyright 2026 Harsshh. All rights reserved.
 *
 * NOTICE: This code is proprietary. Do not copy, modify, or redistribute
 * without proper attribution to the original author.
 */

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
