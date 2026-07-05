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

    // خصم عند الحجز
    public function deductBookingDeposit(User $user, int $appointmentId): bool
    {
        $depositAmount = (float) Setting::get('booking_deposit', 50);

        if ($user->wallet_balance < $depositAmount) {
            return false;
        }

        DB::transaction(function () use ($user, $depositAmount, $appointmentId) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore - $depositAmount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'booking_deduct',
                'amount'         => $depositAmount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => 'Booking deposit deducted',
                'appointment_id' => $appointmentId,
            ]);
        });

        return true;
    }

    // استرداد كامل
    public function refundFull(User $user, int $appointmentId, string $description = 'Full refund'): bool
    {
        $depositAmount = (float) Setting::get('booking_deposit', 50);

        DB::transaction(function () use ($user, $depositAmount, $appointmentId, $description) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter  = $balanceBefore + $depositAmount;

            $user->update(['wallet_balance' => $balanceAfter]);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'refund_full',
                'amount'         => $depositAmount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'appointment_id' => $appointmentId,
            ]);
        });

        return true;
    }

    // استرداد جزئي مع غرامة تصاعدية
    public function refundWithPenalty(User $user, int $appointmentId): bool
    {
        $depositAmount  = (float) Setting::get('booking_deposit', 50);
        $maxPenalty     = (float) Setting::get('max_penalty_percentage', 25);

        // حساب نسبة الغرامة بناءً على عدد المخالفات
        $violationCount  = $user->violation_count + 1;
        $penaltyRate     = min($violationCount * 5, $maxPenalty);
        $penaltyAmount   = $depositAmount * ($penaltyRate / 100);
        $refundAmount    = $depositAmount - $penaltyAmount;

        DB::transaction(function () use ($user, $refundAmount, $penaltyAmount, $depositAmount, $appointmentId, $penaltyRate, $violationCount) {
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

    // جلب المبلغ المبدئي للحجز
    public function getDepositAmount(): float
    {
        return (float) Setting::get('booking_deposit', 50);
    }
}
