<?php declare(strict_types = 1);

include "../vendor/autoload.php";

use Romanzaycev\Tooolooop\Engine;

$engine = new Engine(__DIR__ . '/views');

$template = $engine->make('page')
    ->assign([
        'text' => 'Main <script>alert("Evil stored XSS");</script>
        page
        text',
        'someItems' => [
            'item 1',
            'item 2',
            'item 3'
        ]
    ])
    ->assign(['title' => 'tooloop']);

echo $template->render();
