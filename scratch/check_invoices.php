<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\Appointment;

echo "=== INVOICE STATISTICS ===" . PHP_EOL;
echo "Total Invoices: " . Invoice::count() . PHP_EOL;
echo "Paid Invoices: " . Invoice::where('payment_status', 'paid')->count() . PHP_EOL;
echo "Unpaid Invoices: " . Invoice::where('payment_status', 'unpaid')->count() . PHP_EOL;
echo "Total Revenue (sum of paid): " . Invoice::where('payment_status', 'paid')->sum('total_amount') . PHP_EOL;
echo "Pending Payments (sum of unpaid): " . Invoice::where('payment_status', 'unpaid')->sum('total_amount') . PHP_EOL;
echo PHP_EOL . "=== LIST OF ALL INVOICES ===" . PHP_EOL;
foreach (Invoice::with('appointment')->get() as $inv) {
    echo "Invoice #{$inv->id} -> Status: [{$inv->payment_status}] | Total: \${$inv->total_amount} | Deposit: \${$inv->deposit_amount} | Remaining: \${$inv->remaining_amount} | Appt #{$inv->appointment_id} (Fee: \${$inv->appointment?->consultation_fee}, Add: \${$inv->appointment?->additional_cost})" . PHP_EOL;
}
