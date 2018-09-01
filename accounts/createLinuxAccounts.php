<?php

if(!isset($argv[1]) && !isset($argv[2]))
{
	echo 'Usage: php '.$argv[0].' <path_to_file> <default_password>'.PHP_EOL;
	exit(-1);
}

$file = $argv[1];
$password = $argv[2];

$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
	$line = str_replace("\n", "", $line); //remove new line character
    	exec('useradd -m -s /bin/bash '.$line);
	exec('echo "'.$line.':'.$password.'" | chpasswd');
	echo 'Created account: '.$line.PHP_EOL;
    }

    fclose($handle);
} else {
	echo 'Error opening file'.PHP_EOL;
} 
