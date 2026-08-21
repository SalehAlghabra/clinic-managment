<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\Api\ReportController();
$response = $controller->dashboard();
$data = $response->getData(true);

echo "=== NEW DASHBOARD FINANCIAL STATS ===" . PHP_EOL;
print_r($data['financial']);
