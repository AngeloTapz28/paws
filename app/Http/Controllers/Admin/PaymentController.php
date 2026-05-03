<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['payer', 'application.pet', 'recordedBy'])
            ->when($request->search, fn ($q) =>
                $q->where('reference_number', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type,   fn ($q) => $q->where('type',   $request->type))
            ->latest()
            ->paginate(20);

        $summary = [
            'total_collected' => Payment::where('status', 'completed')->sum('amount'),
            'pending'         => Payment::where('status', 'pending')->count(),
            'today'           => Payment::where('status', 'completed')
                                    ->whereDate('paid_at', today())->sum('amount'),
            'this_month'      => Payment::where('status', 'completed')
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at',  now()->year)
                                    ->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'summary'));
    }

    public function show(Payment $payment)
{
    $payment->load([
        'payer',
        'application.pet',
        'application.adopter',
        'recordedBy',
        'transactions',
    ]);

    return view('admin.payments.show', compact('payment'));
}
}