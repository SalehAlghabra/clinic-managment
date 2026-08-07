<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use App\Models\User;

class WalletController extends Controller
{
    protected WalletService $wallet;
    protected FirebaseService $firebase;

    public function __construct(WalletService $wallet, FirebaseService $firebase)
    {
        $this->wallet   = $wallet;
        $this->firebase = $firebase;
    }

    // عرض رصيد المحفظة
    public function balance(Request $request)
    {
        return response()->json([
            'wallet_balance'  => $request->user()->wallet_balance,
            'violation_count' => $request->user()->violation_count,
            'deposit_amount'  => $this->wallet->getDepositAmount(),
        ]);
    }

    // شحن المحفظة
    // شحن محفظة مريض (الأدمن فقط)
public function deposit(Request $request, $userId)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
    ]);

    $patient = User::find($userId);

    if (!$patient) {
        return response()->json(['message' => 'Patient not found'], 404);
    }

    if ($patient->role !== 'patient') {
        return response()->json(['message' => 'User is not a patient'], 422);
    }

    $this->wallet->deposit(
        $patient,
        $request->amount,
        'Wallet deposit by admin'
    );

    // إشعار للمريض واستحداث سجل في الإشعارات
    app(\App\Services\NotificationService::class)->notify(
        $patient,
        'wallet_deposit',
        'تم شحن المحفظة 💰',
        'Wallet Charged 💰',
        "تم شحن محفظتك بمبلغ \${$request->amount}",
        "Your wallet has been charged with \${$request->amount}",
        'wallet',
        $patient->id,
        ['amount' => (string)$request->amount]
    );

    return response()->json([
        'message'        => 'Wallet charged successfully',
        'patient_name'   => $patient->name,
        'wallet_balance' => $patient->fresh()->wallet_balance,
    ]);
}

    // خصم من محفظة مريض (الأدمن والموظف)
    public function deduct(Request $request, $userId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $patient = User::find($userId);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        if ($patient->role !== 'patient') {
            return response()->json(['message' => 'User is not a patient'], 422);
        }

        if ($patient->wallet_balance < $request->amount) {
            return response()->json(['message' => 'Insufficient patient wallet balance'], 422);
        }

        $this->wallet->deduct(
            $patient,
            $request->amount,
            'Wallet deduction by staff'
        );

        // إشعار للمريض واستحداث سجل في الإشعارات
        app(\App\Services\NotificationService::class)->notify(
            $patient,
            'wallet_deduction',
            'تم خصم رصيد من المحفظة 💸',
            'Wallet Deduction 💸',
            "تم خصم مبلغ \${$request->amount} من محفظتك",
            "An amount of \${$request->amount} has been deducted from your wallet",
            'wallet',
            $patient->id,
            ['amount' => (string)$request->amount]
        );

        return response()->json([
            'message'        => 'Wallet deducted successfully',
            'patient_name'   => $patient->name,
            'wallet_balance' => $patient->fresh()->wallet_balance,
        ]);
    }

    // سجل معاملات المحفظة للمريض الحالي
    public function transactions(Request $request)
    {
        $transactions = WalletTransaction::where('user_id', $request->user()->id)
            ->with('appointment:id,appointment_date,appointment_time')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    // سجل معاملات محفظة مريض محدد (للأدمن والاستقبال)
    public function patientTransactions(Request $request, $userId)
    {
        $transactions = WalletTransaction::where('user_id', $userId)
            ->with('appointment:id,appointment_date,appointment_time')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json($transactions);
    }
}
