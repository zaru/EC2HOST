<?php
$rundir = realpath(dirname($argv[0]));
require $rundir . '/vendor/autoload.php';
require $rundir . '/config.php';

use Aws\Ec2\Ec2Client;

// Change DNS config mode
$mode = (isset($argv['1']) && $argv['1'] == 'hosts') ? 'hosts' : 'dnsmasq';

$client = Ec2Client::factory($config);

$result = $client->describeInstances();

$ec2Lists = array();
foreach ($result['Reservations'] as $item) {
  foreach ($item['Instances'] as $val) {
    if ($val['State']['Code'] == 16) {
      $group = $name = '';
      foreach ($val['Tags'] as $tag) {
        if ($tag['Key'] == 'Group') {
          $group = $tag['Value'];
        } else if ($tag['Key'] == 'Name') {
          $name = $tag['Value'];
        }
      }
      $host = strtolower(($group) ? $group . '.' . $name : $name);
      $pubDnsName = str_replace(
        array('ec2-', '.' . $config['region'] . '.compute.amazonaws.com', '-'), 
        array('', '', '.'),
        $val['PublicDnsName']);
      $ec2Lists[$host] = $pubDnsName;
    }
  }
}

if ($mode == 'hosts') {
  echo generateHosts($ec2Lists);
} else {
  echo generateDnsmasq($ec2Lists);
}

function generateHosts($ec2Lists) {
  if (!file_exists('/etc/hosts')) {
    return;
  }
  
  $hostsFile = file('/etc/hosts');
  $newHostsLine = '';
  foreach ($hostsFile as $line) {
    $flag = false;
    foreach ($ec2Lists as $host => $gIp) {
      $gIp = str_replace('.', '\.', $gIp);
      if (preg_match("/^" . $gIp . "/", $line)) {
        $flag = true;
        break;
      }
    }
    if ($flag === false) {
      $newHostsLine .= $line;
    }
  }
  foreach ($ec2Lists as $host => $gIp) {
    $newHostsLine .= $gIp . ' ' . $host . "\n";
  }
  
  return $newHostsLine;
}

function generateDnsmasq($ec2Lists) {
  $result = '';
  foreach ($ec2Lists as $host => $gIp) {
    $result .= 'address=/' . $host . '/' . $gIp . "\n";
  }

  return $result;
}
