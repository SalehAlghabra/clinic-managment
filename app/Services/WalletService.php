<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    // شحن المحفظة
    public function deposit(User $user, float $amount, string $description = 'Wallet deposit'): bool
    {
        if ($amount <= 0) return false;

        DB::transaction(function () use ($user, $amount, $description) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore + $amount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
            ]);
        });

        return true;
    }

    // خصم يدوي من المحفظة من قبل الأدمن أو الموظف
    public function deduct(User $user, float $amount, string $description = 'Wallet deduction by staff'): bool
    {
        if ($amount <= 0 || $user->wallet_balance < $amount) return false;

        DB::transaction(function () use ($user, $amount, $description) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore - $amount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'deduct',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
            ]);
        });

        return true;
    }

    // خصم عند الحجز (رسوم الكشفية)
    public function deductBookingDeposit(User $user, int $appointmentId, float $amount): bool
    {
        if ($amount <= 0 || $user->wallet_balance < $amount) {
            return false;
        }

        DB::transaction(function () use ($user, $amount, $appointmentId) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore - $amount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'booking_deduct',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => 'Consultation fee deducted',
                'appointment_id' => $appointmentId,
            ]);
        });

        return true;
    }

    // دفع المتبقي من الفاتورة عن طريق المحفظة
    public function payInvoiceFromWallet(User $user, int $appointmentId, float $amount, string $description = 'Invoice payment from wallet'): bool
    {
        if ($amount <= 0) return true;

        if ($user->wallet_balance < $amount) {
            return false;
        }

        DB::transaction(function () use ($user, $amount, $appointmentId, $description) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore - $amount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'booking_deduct',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'appointment_id' => $appointmentId,
            ]);
        });

        return true;
    }

    // استرداد كامل
    public function refundFull(User $user, int $appointmentId, float $amount, string $description = 'Full refund'): bool
    {
        if ($amount <= 0) return true;

        DB::transaction(function () use ($user, $amount, $appointmentId, $description) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore + $amount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'refund_full',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'appointment_id' => $appointmentId,
            ]);
        });

        return true;
    }

    // استرداد جزئي مع غرامة تصاعدية
    public function refundWithPenalty(User $user, int $appointmentId, float $amount): bool
    {
        if ($amount <= 0) return true;

        $maxPenalty = (float) Setting::get('max_penalty_percentage', 25);

        // حساب نسبة الغرامة بناءً على عدد المخالفات
        $violationCount  = $user->violation_count + 1;
        $penaltyRate     = min($violationCount * 5, $maxPenalty);
        $penaltyAmount   = $amount * ($penaltyRate / 100);
        $refundAmount    = $amount - $penaltyAmount;

        DB::transaction(function () use ($user, $refundAmount, $penaltyAmount, $amount, $appointmentId, $penaltyRate, $violationCount) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore + $refundAmount;

            // تحديث المحفظة وعداد المخالفات
            $user->update([
                'wallet_balance'  => $balanceAfter,
                'violation_count' => $violationCount,
            ]);

            // تسجيل الاسترداد الجزئي
            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'refund_partial',
                'amount'         => $refundAmount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => "Partial refund after {$penaltyRate}% penalty (violation #{$violationCount})",
                'appointment_id' => $appointmentId,
            ]);

            // تسجيل الغرامة
            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'penalty',
                'amount'         => $penaltyAmount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => "Penalty {$penaltyRate}% for late cancellation",
                'appointment_id' => $appointmentId,
            ]);
        });

        return true;
    }

    // جلب رصيد المحفظة
    public function getBalance(User $user): float
    {
        return (float) $user->wallet_balance;
    }

    // جلب مبلغ الشحن المخفض / الإيداع
    public function getDepositAmount(): float
    {
        return (float) Setting::get('deposit_amount', 0);
    }
}

