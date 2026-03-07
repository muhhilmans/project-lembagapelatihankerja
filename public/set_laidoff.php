<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$application = App\Models\Application::with('servant:id,name', 'employe:id,name')
    ->where('employe_id', 'a1255e54-0ce3-4992-97fa-dc9dc96e9aa8')
    ->whereIn('status', ['accepted', 'contract', 'choose'])
    ->first();

if ($application) {
    $application->status = 'laidoff';
    $application->save();
    echo "Application " . $application->id . " set to laidoff.\n";
    echo "Servant: " . ($application->servant->name ?? 'None') . "\n";
    echo "Employer: " . ($application->employe->name ?? 'None') . "\n";
} else {
    echo "No active application found.\n";
}
