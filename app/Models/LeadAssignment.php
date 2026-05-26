<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAssignment extends Model
{
    protected $fillable = [
        'lead_id',
        'from_user_id',
        'to_user_id',
        'changed_by_user_id',
        'reason',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
