<?php

if(!isset($argv[1]))
{
	echo 'Usage: php '.$argv[0].' <path_to_file>'.PHP_EOL;
	exit(-1);
}

$file = $argv[1];

$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
	$line = str_replace("\n", "", $line); //remove new line character
    	exec('useradd -m -s /bin/bash '.$line);
	exec('echo "'.$line.':ACES@12345" | chpasswd');
	echo 'Created account: '.$line.PHP_EOL;
    }

    fclose($handle);
} else {
	echo 'Error opening file'.PHP_EOL;
} 
