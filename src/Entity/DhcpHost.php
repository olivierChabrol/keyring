<?php
// src/Controller/LuckyController.php
namespace App\Entity;

use \Datetime;
use \JsonSerializable;

class DhcpHost implements JsonSerializable
{
    private $_hostname       = NULL;
    private $_creator        = NULL;
    private $_expirationDate = NULL;
    private $_host           = NULL;
    private $_macAddress     = NULL;
    private $_fixedIpAddress = NULL;
    private $_dateModif      = NULL;
    private $_commentary     = NULL;
    private $_line           = NULL;

	public static function mySubString($str, $beginPattern, $endPattern)
	{
		$bpPos = strpos($str, $beginPattern);
		if ($bpPos !== False)
		{
			$bpPos += strlen($beginPattern);
			$epPos  = strpos($str, $endPattern, $bpPos);
			return trim(substr($str, $bpPos, $epPos - $bpPos));
		}
		else
			return NULL;
	}

	public static function getDhcpHost($line1, $line2, $line3)
	{
		$dhcpHost = new DhcpHost();


		if ($line1  == NULL && $line2  == NULL and $line3 == NULL) {
                $dhcpHost->_line = " ";
                return NULL;
		}
		else {
                $expirationDate = "";
                if ($line2 == NULL) {
                    $expirationDate = "01-01-0001";
				}
                else {
                    $expirationDate = substr($line2, strpos($line2, ":")+1);
                    if (trim($expirationDate) == "")
                    {
						$expirationDate = "01-01-0001";
					}
				}
				//if (strpos($line1, "# 2019-04-11 09:47:24") !== False)
				//{
				  //echo "[DHCPHOST] $expirationDate \n" ;
				  //echo "[DHCPHOST] \$line2 = $line2 \n" ;
				  //exit (1);
				//}

                $expirationDate = trim($expirationDate);
                $dhcpHost->_expirationDate = $expirationDate;
                $line3 = preg_replace('/\s+/', ' ',$line3);
                $line3Split = explode(" ", $line3);
                $dhcpHost->setHostname($line3Split[1]);
                $dhcpHost->setMacAddress(DhcpHost::mySubString($line3, "hardware ethernet ", ";"));
                $dhcpHost->setFixedIpAddress(DhcpHost::mySubString($line3, "fixed-address ",";"));
                //$dhcpHost->setHostname($line3);

                $starPos = strpos($line1, "*");
                if ($starPos === False)
                {
					if (strpos($line2, "# Expire") !== False)
					{
						$line1 = trim($line1);
						$dhcpHost->setCommentary($line1);
					}
					else
					{
						$dhcpHost->setCommentary($line1.$line2);
					}
				}
				else
				{
					$line1 = substr($line1, 1);
					$ex    = explode("*", $line1);
					$dateModifStr = trim($ex[0]);
					$dateTime = DateTime::createfromformat('Y-m-d H:i:s',$dateModifStr);
					$dhcpHost->setDateModif($dateTime);

					$dhcpHost->setCreator(trim($ex[1]));
					$dhcpHost->setCommentary(trim($ex[2]));
				}
		}
		//			echo $dhcpHost->toDhcpString();
		return $dhcpHost;
	}

	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);

		return $length === 0 ||
		(substr($haystack, -$length) === $needle);
	}

	public function toDhcpString() {
		$retour = "";

		if (substr_count($this->_commentary, "\n") >=1 || substr_count($this->_commentary, "\r") >=1){
			$this->_commentary = str_replace ("\r\n","|", $this->_commentary);
			//$this->_commentary = str_replace ("\n","|", $this->_commentary);
			//$this->_commentary = str_replace ("\r","|", $this->_commentary);
			//$this->_commentary = preg_replace ("\r\n","|", $this->_commentary);
		}

		if ($this->_creator == NULL) {
			$retour = $this->_commentary;
			$retour .= "\n";
			$retour .= "# Expire : ".$this->_expirationDate."\n";
		}
		else {
			$retour  = "# " . $this->_dateModif->format('Y-m-d H:i:s') . " * " . $this->_creator . " * " . $this->_commentary ;
			if ($this->_commentary != NULL && !DhcpHost::endsWith($this->_commentary, "\n")) {
				$retour .= "\n";
			}
			$retour .= "# Expire : ".$this->_expirationDate."\n";

		}

		$retour .= "host ".$this->_hostname." { hardware ethernet ".$this->_macAddress."; ";
		if ($this->_fixedIpAddress != NULL) {
			$retour .= "fixed-address ".$this->_fixedIpAddress."; ";
		}
		$retour .= "}\n";
		return $retour;
	}

	public function getCreator()
	{
		return $this->_creator;
	}

	public function setCreator($creator)
	{
		$this->_creator = $creator;
	}

	public function getExpirationDate()
	{
		return $this->_expirationDate;
	}

	public function setExpirationDate($expirationDate)
	{
		$this->_expirationDate = $expirationDate;
	}

	public function getCommentaryHtml()
	{
		return str_replace("|","\n", $this->_commentary);
	}

	public function getCommentary()
	{
		return $this->_commentary;
	}

	public function setCommentary($commentary)
	{
		$this->_commentary = $commentary;
	}

	public function getDateModif()
	{
		return $this->_dateModif;
	}

	public function setDateModif($dateModif)
	{
		$this->_dateModif = $dateModif;
	}


	public function getFixedIpAddress()
	{
		return $this->_fixedIpAddress;
	}

	public function setFixedIpAddress($fixedIPAddress)
	{
		$this->_fixedIpAddress = $fixedIPAddress;
	}

	public function getMacAddress()
	{
		return $this->_macAddress;
	}

	public function setMacAddress($macAddress)
	{
		$this->_macAddress = $macAddress;
	}

	public function setHostname($hostname)
	{
		$this->_hostname = $hostname;
	}

	public function getHostname()
	{
		return $this->_hostname;
	}

	public function __toString()
    {
        return $this->_hostname." ".$this->_expirationDate;
    }


     public function jsonSerialize() {
        return [
            'hostname' => $this->_hostname,
            'dateModif' => ($this->_dateModif == null ?"01-01-0001":$this->_dateModif->format('d-m-Y')),
            'macAddress' => $this->_macAddress,
            'fixedIpAddress' => ($this->_fixedIpAddress == null ?"":$this->_fixedIpAddress)

        ];
    }
}
