<?php

declare(strict_types=1);

use JsonStreamingParser\Listener\RegexListener;
use JsonStreamingParser\Parser;
use Psl\File;

require_once __DIR__ . '/../vendor/autoload.php';

$filename = __DIR__ . '/../tests/data/example.json';

echo "Check where the 'name' elements are ('(.*/name)')..." . PHP_EOL;
$listener = new RegexListener(['(.*/name)' => static function ($data, $path): void {
    echo 'Location is ' . $path . ' value is ' . $data . PHP_EOL;
}]);
$fp = File\open_read_only($filename);
$parser = new Parser($fp, $listener);
$parser->parse();
$fp->close();

echo PHP_EOL . "Extract the second 'name' element ('/1/name')..." . PHP_EOL;
$listener = new RegexListener(['/1/name' => static function ($data): void {
    echo "Value for '/1/name' is " . $data . PHP_EOL;
}]);
$fp = File\open_read_only($filename);
$parser = new Parser($fp, $listener);
$parser->parse();
$fp->close();

echo PHP_EOL . "Extract each base element ('(/\d*)') and print 'name' element of this..." . PHP_EOL;
$listener = new RegexListener(['(/\d*)' => static function ($data, $path): void {
    echo 'Location is ' . $path . ' value is ' . $data['name'] . PHP_EOL;
}]);
$fp = File\open_read_only($filename);
$parser = new Parser($fp, $listener);
$parser->parse();
$fp->close();

echo PHP_EOL . "Extract 'nested array' element ('(/.*/nested array)')..." . PHP_EOL;
$listener = new RegexListener(['(/.*/nested array)' => static function ($data, $path): void {
    echo 'Location is ' . $path . ' value is ' . print_r($data, true) . PHP_EOL;
}]);
$fp = File\open_read_only($filename);
$parser = new Parser($fp, $listener);
$parser->parse();
$fp->close();

echo PHP_EOL . PHP_EOL . 'Combine above...' . PHP_EOL;
$listener = new RegexListener([
    '/1/name' => static function ($data): void {
        echo PHP_EOL . "Extract the second 'name' element..." . PHP_EOL;
        echo '/1/name=' . print_r($data, true) . PHP_EOL;
    },
    '(/\d*)' => static function ($data, $path): void {
        echo PHP_EOL . "Extract each base element and print 'name'..." . PHP_EOL;
        echo $path . '=' . $data['name'] . PHP_EOL;
    },
    '(/.*/nested array)' => static function ($data, $path): void {
        echo PHP_EOL . "Extract 'nested array' element..." . PHP_EOL;
        echo $path . '=' . print_r($data, true) . PHP_EOL;
    },
]);
$fp = File\open_read_only($filename);
$parser = new Parser($fp, $listener);
$parser->parse();
$fp->close();

$filename = __DIR__ . '/../tests/data/ratherBig.json';

echo 'With a large file extract totals from header and stop...' . PHP_EOL;
$listener = new RegexListener();
$fp = File\open_read_only($filename);
$parser = new Parser($fp, $listener);
$listener->setMatch(['/total_rows' => static function ($data) use ($parser): void {
    echo '/total_rows=' . $data . PHP_EOL;
    $parser->stop();
}]);
$parser->parse();
$fp->close();
