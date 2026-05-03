<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\SystemNotification;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['payer', 'application.pet', 'recordedBy'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->search, fn ($q) => $q->where('reference_number', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15);

        $summary = [
            'total_collected' => Payment::where('status', 'completed')->sum('amount'),
            'pending'         => Payment::where('status', 'pending')->count(),
            'today'           => Payment::where('status', 'completed')->whereDate('paid_at', today())->sum('amount'),
        ];

        return view('staff.payments.index', compact('payments', 'summary'));
    }

    public function create(): View
    {
        $adopters = \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'adopter'))->get();
        $applications = \App\Models\AdoptionApplication::with(['pet', 'adopter'])
            ->whereIn('status', ['approved'])
            ->get();
        return view('staff.payments.create', compact('adopters', 'applications'));
    }

    /**
     * Record payment — wrapped in DB::transaction with Transaction log.
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['recorded_by'] = auth()->id();

            // Auto-set payer from the linked application
            if ($data['adoption_application_id'] ?? null) {
                $app = \App\Models\AdoptionApplication::find($data['adoption_application_id']);
                $data['payer_id'] = $app->adopter_id;
            }

            if ($request->hasFile('proof_of_payment')) {
                $data['proof_of_payment'] = $request->file('proof_of_payment')
                    ->store('payment-proofs', 'public');
            }

            // Create payment
            $payment = Payment::create($data);

            // Create transaction audit record
            Transaction::create([
                'payment_id' => $payment->id,
                'amount'     => $payment->amount,
                'type'       => 'credit',
                'status'     => $payment->status === 'completed' ? 'success' : 'pending',
                'notes'      => "Payment recorded by staff: " . auth()->user()->name,
            ]);

            // If payment completes an adoption, mark application as completed
            if ($payment->status === 'completed' && $payment->adoption_application_id) {
                $payment->application->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                // Notify adopter
                SystemNotification::create([
                    'user_id' => $payment->application->adopter_id,
                    'title'   => '🎉 Adoption Complete!',
                    'message' => "Your payment of ₱" . number_format($payment->amount, 2) .
                                 " has been confirmed. {$payment->application->pet->name} is now yours!",
                    'type'    => 'success',
                    'icon'    => 'bi-check-circle-fill',
                ]);
            }

            ActivityLog::log(
                'payment_recorded',
                "Payment {$payment->reference_number} of ₱" . number_format($payment->amount, 2) . " recorded.",
                $payment
            );
        });

        return redirect()->route('staff.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['payer', 'application.pet', 'recordedBy', 'transactions']);
        return view('staff.payments.show', compact('payment'));
    }

    public function update(StorePaymentRequest $request, Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($request, $payment) {
            $oldStatus = $payment->status;
            $payment->update($request->validated());

            // Log status change as new transaction entry
            if ($oldStatus !== $payment->status) {
                Transaction::create([
                    'payment_id' => $payment->id,
                    'amount'     => $payment->amount,
                    'type'       => $payment->status === 'refunded' ? 'refund' : 'credit',
                    'status'     => $payment->status === 'completed' ? 'success' : $payment->status,
                    'notes'      => "Status updated from {$oldStatus} to {$payment->status}.",
                ]);
            }

            ActivityLog::log('payment_updated', "Payment {$payment->reference_number} updated.", $payment);
        });

        return redirect()->route('staff.payments.show', $payment)
            ->with('success', 'Payment updated.');
    }
}