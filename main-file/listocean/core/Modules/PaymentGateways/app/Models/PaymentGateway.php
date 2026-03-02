<?php

namespace Modules\PaymentGateways\app\Models;

use App\Enums\StatusEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PaymentGateways\Database\factories\PaymentGatewayFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'payment_gateways';
    protected $fillable = ['name','image','description','status','test_mode','credentials'];

    protected $casts = [
        'test_mode' => 'integer',
        'status' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnums::PUBLISH);
    }
}
