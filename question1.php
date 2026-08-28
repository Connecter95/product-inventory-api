<?php

$array = [];

echo "Populating array with 100000000 random numbers..\n\n";

$start_memory = memory_get_usage();
printf("Checkpoint 1: Memory Usage %f bytes\n", $start_memory);

for ($i = 0; $i < 100000000; $i++) {

    $num = rand(0, 10000000);

    // Store the random number as the array key.
    // Duplicated numbers automatically overwrite the same key.
    $array[$num] = true;
}

// Make sure the match key exists so we can check it
// without using array_key_exists() / isset().
$array[1] = $array[1] ?? false;

$start_time = round(microtime(true) * 1000);
printf("Checkpoint 2: %fms.\n", $start_time);

// Number to be matched
$match = 1;

$found = false;

// Direct hash lookup instead of looping through 10+ million values.
if ($array[$match] === true) {
    $found = true;
}

$end_time = round(microtime(true) * 1000);
$end_memory = memory_get_usage();

$time_diff = $end_time - $start_time;
$memory_diff = round(($end_memory - $start_memory) / 1024 / 1024, 4);

printf("Checkpoint 3: %fms. Memory Usage %f bytes\n\n", $end_time, $end_memory);
printf("Time used: %fms\n", $time_diff);
printf("Memory used: %f MB\n\n", $memory_diff);

printf("Match found: %s\n", ($found ? 'Y' : 'N'));
