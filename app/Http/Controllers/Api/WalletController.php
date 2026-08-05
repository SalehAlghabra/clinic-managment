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

    // إشعار للمريض
    if ($patient->fcm_token) {
        $this->firebase->sendNotification(
            $patient->fcm_token,
            'Wallet Charged 💰',
            "Your wallet has been charged with \${$request->amount}",
            ['type' => 'wallet_deposit']
        );
    }

    return response()->json([
        'message'        => 'Wallet charged successfully',
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
}
