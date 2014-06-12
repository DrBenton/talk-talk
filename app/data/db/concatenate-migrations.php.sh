#!/usr/bin/php
<?php

// Config
$migrationsFilesPattern = __DIR__ . '/migrations/*.sql';
$targetFilePath = __DIR__ . '/migrations-concatenated.sql';

// Go!
$timeStart = microtime(true);
$migrationsFiles = glob($migrationsFilesPattern);
sort($migrationsFiles);

echo "Starting migrations concatenation of \033[32m".sizeof($migrationsFiles)."\033[0m SQL files to ".$targetFilePath."... " . PHP_EOL;

$outputFile = fopen($targetFilePath, 'w');
foreach($migrationsFiles as $migrationFilePath) {
    fwrite($outputFile, PHP_EOL . PHP_EOL . "-- (from $migrationFilePath)" . PHP_EOL);
    fwrite($outputFile, file_get_contents($migrationFilePath) . PHP_EOL);
}

fclose($outputFile);

$duration = microtime(true) - $timeStart;
echo PHP_EOL . "✓ Finished. (in ".round($duration, 2)."s.)" . PHP_EOL;