<?php

declare(strict_types=1);

use JsonStreamingParser\Listener\InMemoryListener;
use JsonStreamingParser\Parser;
use Psl\Async;
use Psl\IO;

require_once __DIR__ . '/../vendor/autoload.php';


$stdout = IO\output_handle();
[$read, $write] = IO\pipe();

Async\concurrent([
    static function() use($read, $stdout): void {
        $listener = new InMemoryListener();
        $parser = new Parser($read, $listener);
        $stdout->writeAll("< parsing started.\n");

        $parser->parse();
        $read->close();

        $stdout->writeAll("< parsing finished.\n");
        var_dump($listener->getJson());
    },
    static function() use($write, $stdout): void {
        $stdout->writeAll("> sleeping.\n");
        Async\usleep(1000);
        $stdout->writeAll("> sending some data.\n");
        $write->writeAll('{ "name": "saif"');
        $stdout->writeAll("> sleeping more.\n");
        Async\usleep(10000);
        $stdout->writeAll("> sending more data.\n");
        $write->writeAll(', "email": "azjezz@protonmail.com"}');
        $stdout->writeAll("> closing.\n");
        $write->close();
    },
]);
