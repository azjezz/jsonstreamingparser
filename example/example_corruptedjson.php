<?php

declare(strict_types=1);

use JsonStreamingParser\Listener\CorruptedJsonListener;
use JsonStreamingParser\Parser;
use Psl\File;

require_once __DIR__ . '/../vendor/autoload.php';

$listener = new CorruptedJsonListener();

$handle = File\open_read_only(__DIR__ . '/../tests/data/example.geojson');
$lock = $handle->lock(File\LockType::SHARED);

try {
    $parser = new Parser($handle, $listener);
    $parser->parse();
} finally {
    $lock->release();
    $handle->close();
}

$listener->forceEndDocument();
//get repaired json
print_r($listener->getJson());
