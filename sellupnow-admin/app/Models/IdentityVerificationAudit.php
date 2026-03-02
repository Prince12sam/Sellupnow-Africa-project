<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentityVerificationAudit extends Model
{
    protected $table = 'identity_verification_audits';
    protected $connection = 'listocean';
    protected $fillable = [
        'verification_id',
        'user_id',
        'admin_id',
        'action',
        'reason',
    ];
}
