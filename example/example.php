<?php

declare(strict_types=1);

use JsonStreamingParser\Listener\InMemoryListener;
use JsonStreamingParser\Parser;
use Psl\File;

require_once __DIR__ . '/../vendor/autoload.php';

$listener = new InMemoryListener();

$handle = File\open_read_only(__DIR__ . '/../tests/data/example.json');
$lock = $handle->lock(File\LockType::SHARED);

try {
    $parser = new Parser($handle, $listener);
    $parser->parse();
} finally {
    $lock->release();
    $handle->close();
}

print_r($listener->getJson());
