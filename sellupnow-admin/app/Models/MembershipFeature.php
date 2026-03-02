<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipFeature extends Model
{
    protected $table = 'membership_features';
    protected $fillable = ['key', 'label', 'description', 'is_active'];
}
