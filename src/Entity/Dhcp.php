<?php
// src/Controller/LuckyController.php
namespace App\Entity;

use App\Entity\DhcpHost;

class Dhcp
{
	private $_filename = NULL;
	private $_lineCount = 0;
	private $_fileBegining = NULL;
	private $_hosts = NULL;
	
	
	public static function readDhcpFile($filename)
	{
		$lineCount = 0;
		
		$handle = fopen($filename, "r");
		if ($handle) {
			
			$fileBegining = "";
			$beginHost = False;
			$array     = array();
			$commentaire1 = NULL;
			$commentaire2 = NULL;
			
			$a = array();
			
			while (($line = fgets($handle)) !== False) {
				$lineCount++;
				
				if (!$beginHost) { $array[] = $line;}
				if (strlen($line) == 0) {
					$commentaire1 = NULL;
					$commentaire2 = NULL;
				}
				
				$diezePos = strpos($line, "#");
				if ($diezePos!== False && $diezePos == 0) {
					if ($commentaire1 == NULL) {
						$commentaire1 = $line;
					} else if ($commentaire2 == NULL) {
						$commentaire2 = $line;
					}
				}
				
				$hostPos = strpos($line, "host ");
				if ($hostPos !== False && $hostPos == 0) {
					$beginHost = True;
					$dhcpHost = DhcpHost::getDhcpHost($commentaire1, $commentaire2, $line);
					//$a[$dhcpHost.gethostname()] = $dhcpHost;
					$a += [$dhcpHost->getHostname() => $dhcpHost];
					//print($dhcpHost.getHostname()."<br>");
					$commentaire1 = NULL;
					$commentaire2 = NULL;
				}
				
				
				if ($lineCount > 3) {
					$commentaire1 = $commentaire2;
					$commentaire2 = $line;
				}
			}
			fclose($handle);
		} else {
			// error opening the file.
		}
		
		// remove the last 3 elements
		array_pop($array);
		array_pop($array);
		array_pop($array);
				
		$dhcp = new Dhcp;
		$dhcp->_lineCount = $lineCount;
		$dhcp->_filename  = $filename;
		$dhcp->_hosts     = $a;
		$dhcp->_fileBegining = $fileBegining;
		
		foreach ($array as $elm) {
			$dhcp->_fileBegining .= $elm;
		}
		
		//var_dump($a);
		return $dhcp;
	}
	
	public static function getBackupName($filename) {
		$lastDotPos = strrpos($filename, ".");
		if ($lastDotPos !== False) {
			$substring = substr($filename, 0, $lastDotPos);
			$substring .= "_";
			$date = new \DateTime('now');
			$substring .= $date->format('dmY_Gis');
			$substring .= ".conf";
			return $substring;
		}
		return "";
	}
	
	public static function saveDhcp($dhcp) {
		$backupFile = Dhcp::getBackupName($dhcp->getFilename());
		if (!copy($dhcp->getFilename(), $backupFile)) {
			echo "failed to copy";
			return;
		}
		$fp = fopen($dhcp->getFilename(), 'w');
		fwrite($fp, $dhcp->getFileBegining());
		foreach ($dhcp->getHosts() as $host) {
			$s = $host->toDhcpString();
			fwrite($fp, $s);
		}
		fclose($fp);
		Dhcp::restartDhcpService();
	}
	
	public static function restartDhcpService() {
		$systemreturn=exec("sudo /etc/init.d/isc-dhcp-server restart",$sys);
	}
	
	public function getFileBegining() {
		return $this->_fileBegining;
	}
	
	public function getHost($hostname)
	{
		return $this->_hosts[$hostname];
	}
	public function getCle($cle)
	{
		return $this->_trousseaux[$cle];
	}

	public function addHost($hostname, $host)
	{
			$this->_hosts[$hostname] = $host;
	}

	public function hostExist($hostname)
	{
		return array_key_exists($hostname, $this->_hosts);
	}
	
	public function setHosts($hosts)
	{
		return $this->_hosts = $hosts;
	}
	public function getHosts()
	{
		return $this->_hosts;
	}
	
	public function getLineCount()
	{
		return $this->_lineCount;
	}
	
	public function getFilename()
	{
		return $this->_filename;
	}
}
