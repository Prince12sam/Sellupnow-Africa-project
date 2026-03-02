<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Actual DB state (tables created by an earlier stub migration):
 *
 * wallets          -> id, user_id, balance, status, created_at, updated_at
 * wallet_histories -> id, user_id, payment_gateway, payment_status, amount,
 *                    transaction_id, manual_payment_image, status, created_at, updated_at
 *
 * Adds columns our WalletService needs while keeping the existing columns intact.
 */
class ExtendWalletTablesForAdminCompat extends Migration
{
    public function up()
    {
        // wallets: add currency column
        if (Schema::hasTable('wallets') && ! Schema::hasColumn('wallets', 'currency')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->string('currency', 10)->default('GHS')->after('balance');
            });
        }

        // wallet_histories: add service columns
        if (Schema::hasTable('wallet_histories')) {
            Schema::table('wallet_histories', function (Blueprint $table) {
                if (! Schema::hasColumn('wallet_histories', 'type')) {
                    $table->string('type', 50)->nullable()->after('user_id')
                          ->comment('credit|debit|admin_credit|admin_debit|gateway_topup');
                }
                if (! Schema::hasColumn('wallet_histories', 'balance_after')) {
                    $table->decimal('balance_after', 15, 2)->nullable()->after('amount');
                }
                if (! Schema::hasColumn('wallet_histories', 'note')) {
                    $table->text('note')->nullable()->after('balance_after');
                }
                if (! Schema::hasColumn('wallet_histories', 'reference_type')) {
                    $table->string('reference_type', 100)->nullable()->after('note');
                }
                if (! Schema::hasColumn('wallet_histories', 'reference_id')) {
                    $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
                }
                if (! Schema::hasColumn('wallet_histories', 'metadata')) {
                    $table->json('metadata')->nullable()->after('reference_id');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('wallet_histories')) {
            Schema::table('wallet_histories', function (Blueprint $table) {
                foreach (['type', 'balance_after', 'note', 'reference_type', 'reference_id', 'metadata'] as $col) {
                    if (Schema::hasColumn('wallet_histories', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('wallets') && Schema::hasColumn('wallets', 'currency')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropColumn('currency');
            });
        }
    }
}
