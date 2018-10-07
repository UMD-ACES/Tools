<?php

require '../vendor/autoload.php';

if(!isset($argv[1]) || !isset($argv[2]))
{
    echo 'Usage: php '.$argv[0].' subnets groups <excludedIPs>'.PHP_EOL;
    echo 'Example: php '.$argv[0].' 10.0.0.0/16,10.1.0.0/16 CLASS100_1A,CLASS100_1B 10.0.0.1,10.1.0.1'.PHP_EOL;
    exit();
}

// Pulling information from command line

$subnets    = $argv[1]; // Example: 10.0.0.0/16,10.1.0.0/16
$groups     = $argv[2]; // Example: CLASS100_1A,CLASS100_1B

$excludedIPs = '';
if(isset($argv[3]))
{
    $excludedIPs = $argv[3]; // Example: 10.0.0.1,10.1.0.1
}

// Transforming data to be used for the application

$subnets    = explode(',', $subnets);
$groups     = explode(',', $groups);

if($excludedIPs != '')
{
    $excludedIPs = explode(',', $excludedIPs);
}
else
{
    $excludedIPs = array();
}

$hosts = array();

// Find the hosts in each subnet (remove network + broadcast)
foreach ($subnets as &$subnet)
{
    $subnet = \IPTools\Network::parse($subnet);

    foreach ($subnet->getHosts() as $host)
    {
        $hosts[] = (string) $host;
    }
}

// Remove excluded IPs
$hosts = array_values(array_diff($hosts, $excludedIPs));

// Calculate the number of IPs Per Group
$IPsRemainder = count($hosts) % count($groups);

$IPsPerGroup   = (count($hosts) - $IPsRemainder) / count($groups);

echo 'Unallocated IPs:'. $IPsRemainder.PHP_EOL;
echo 'IPs Per Group:'.$IPsPerGroup.PHP_EOL;

// Assign the IPs to groups
$IPsAssignedToGroups = array();

$hostIndex = 0;

for ($i = 0; $i < $IPsPerGroup; $i++)
{
    foreach ($groups as $group)
    {
        if($i == 0)
        {
            $IPsAssignedToGroups[$group] = array($hosts[$hostIndex]);
        }
        else
        {
            $IPsAssignedToGroups[$group][] = $hosts[$hostIndex];
        }

        $hostIndex++;
    }
}

foreach ($groups as $group)
{
    echo $group.': '.implode(', ', $IPsAssignedToGroups[$group]).PHP_EOL;
}