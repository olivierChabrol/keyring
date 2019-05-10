<?php

include "/var/www/dhcp/src/Entity/Dhcp.php";
include "/var/www/dhcp/src/Entity/DhcpHost.php";

use App\Entity\DhcpHost;
use App\Entity\Dhcp;

$dhcp = Dhcp::readDhcpFile("/etc/dhcp/dhcpd.conf");

