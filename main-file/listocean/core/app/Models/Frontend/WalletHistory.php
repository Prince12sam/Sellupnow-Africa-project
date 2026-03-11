<?php

namespace App\Models\Frontend;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletHistory extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // wallet_histories is append-only, no updated_at column

    protected $fillable = [
        'user_id', 'type', 'amount', 'balance_after',
        'reference_type', 'reference_id', 'note',
        'payment_gateway', 'payment_status', 'transaction_id',
        'manual_payment_image', 'status', 'metadata',
    ];


    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata'      => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCreditType(): bool
    {
        return $this->type === 'credit';
    }
}
