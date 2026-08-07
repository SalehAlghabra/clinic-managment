<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::whereNotNull('profile_picture')->get(['id','name','role','profile_picture']);
foreach ($users as $u) {
    echo json_encode([
        'id' => $u->id,
        'name' => $u->name,
        'role' => $u->role,
        'profile_picture' => $u->profile_picture,
        'profile_picture_url' => $u->profile_picture_url,   // via getProfilePictureUrlAttribute()
    ]) . PHP_EOL;
}
if ($users->isEmpty()) echo "No users with profile pictures found.\n";
