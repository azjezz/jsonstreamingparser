<?php

declare(strict_types=1);

use JsonStreamingParser\Listener\GeoJsonListener;
use JsonStreamingParser\Parser;
use Psl\File;

require_once __DIR__.'/../vendor/autoload.php';

$filename = __DIR__.'/../tests/data/example.geojson';

$listener = new GeoJsonListener(static function ($item): void {
    var_dump($item);
});
$file = File\open_read_only($filename);
try {
    $parser = new Parser($file, $listener);
    $parser->parse();
} finally {
    $file->close();
}
