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
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'mobile_number',
        'email',
        'source',
        'status',
    ];

    /**
     * Valid lead statuses.
     */
    const STATUSES = ['new', 'contacted', 'interested', 'follow_up', 'won', 'lost'];

    /**
     * Get the user that owns the lead.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the follow-ups for this lead.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get pending follow-ups for this lead.
     */
    public function pendingFollowUps()
    {
        return $this->hasMany(FollowUp::class)->where('status', 'pending');
    }
}
