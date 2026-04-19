<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sections = App\Models\Section::query()
    ->withCount('lessons')
    ->where('grade', 9)
    ->where('is_revision', true)
    ->orderBy('order')
    ->orderBy('id')
    ->get(['id','title','title_kk','order','grade','is_revision']);

foreach ($sections as $section) {
    echo json_encode([
        'id' => $section->id,
        'title' => $section->title,
        'order' => $section->order,
        'lessons_count' => $section->lessons_count,
    ], JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
