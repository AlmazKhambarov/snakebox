<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->handleRequest(\Illuminate\Http\Request::capture());

$boxes = \App\Models\Boxes::all();
$appUrl = config('app.url');

foreach ($boxes as $box) {
    if ($box->image && substr($box->image, 0, 4) !== 'http') {
        $filename = basename($box->image);
        $box->image = $appUrl . '/storage/boxes/' . $filename;
        $box->save();
        echo "Updated: {$box->name} -> {$box->image}\n";
    }
}

echo "Done!\n";
