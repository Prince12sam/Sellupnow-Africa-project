<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGatewayRequest;
use App\Models\PaymentGateway;
use App\Repositories\PaymentGatewayRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentGatewayController extends Controller
{
    /**
     * Show payment gateway
     */
    public function index()
    {
        $paymentGateways = PaymentGatewayRepository::getAll();

        $hasListoceanPaymentGatewaysTable = false;
        $listoceanPaystack = null;
        try {
            $hasListoceanPaymentGatewaysTable = Schema::connection('listocean')->hasTable('payment_gateways');
            if ($hasListoceanPaymentGatewaysTable) {
                $listoceanPaystack = DB::connection('listocean')
                    ->table('payment_gateways')
                    ->where('name', 'paystack')
                    ->first();
            }
        } catch (\Throwable $th) {
            $hasListoceanPaymentGatewaysTable = false;
            $listoceanPaystack = null;
        }

        return view('admin.payment-gateway.index', compact('paymentGateways', 'hasListoceanPaymentGatewaysTable', 'listoceanPaystack'));
    }

    /**
     * Update payment gateway
     */
    public function update(PaymentGatewayRequest $request, PaymentGateway $paymentGateway)
    {
        PaymentGatewayRepository::updateByRequest($request, $paymentGateway);

        return back()->withSuccess(__('Payment Gateway Updated Successfully'));
    }

    /**
     * Toggle payment gateway status
     */
    public function toggle(PaymentGateway $paymentGateway)
    {
        $paymentGateway->update([
            'is_active' => ! $paymentGateway->is_active,
        ]);

        return back()->withSuccess(__('Status Updated Successfully'));
    }

    /**
     * Update customer web Paystack credentials in the customer web DB.
     */
    public function updateListoceanPaystack(Request $request)
    {
        $request->validate([
            'public_key'     => ['required', 'string', 'max:255'],
            'secret_key'     => ['required', 'string', 'max:255'],
            'merchant_email' => ['nullable', 'string', 'max:255'],
            'currency'       => ['required', 'string', 'in:NGN,GHS,ZAR,USD,KES,EGP'],
            'channels'       => ['nullable', 'array'],
            'channels.*'     => ['string', 'in:card,bank,ussd,mobile_money,bank_transfer,qr,eft'],
            'test_mode'      => ['nullable'],
            'status'         => ['nullable'],
        ]);

        abort_unless(Schema::connection('listocean')->hasTable('payment_gateways'), 400, 'ListOcean payment_gateways table not found');

        $credentials = [
            'public_key'     => $request->input('public_key'),
            'secret_key'     => $request->input('secret_key'),
            'merchant_email' => $request->input('merchant_email'),
            'currency'       => strtoupper($request->input('currency', 'NGN')),
            'channels'       => $request->input('channels', []),
        ];

        $now = now();

        $existing = DB::connection('listocean')->table('payment_gateways')->where('name', 'paystack')->first();
        if ($existing) {
            DB::connection('listocean')->table('payment_gateways')->where('id', $existing->id)->update([
                'credentials' => json_encode($credentials),
                'test_mode' => $request->boolean('test_mode') ? 1 : 0,
                'status' => $request->boolean('status') ? 1 : 0,
                'updated_at' => $now,
            ]);
        } else {
            DB::connection('listocean')->table('payment_gateways')->insert([
                'name'        => 'paystack',
                'image'       => null,
                'description' => null,
                'credentials' => json_encode($credentials),
                'test_mode'   => $request->boolean('test_mode') ? 1 : 0,
                'status'      => $request->boolean('status') ? 1 : 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        return back()->withSuccess(__('Customer web Paystack settings updated'));
    }
}
