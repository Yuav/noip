<?php
class noip {
	private $static_ip, $username, $password, $hostname;
	private $update_url = 'dynupdate.no-ip.com';
	 protected $_useragent = 'Skarpeteig Update Client (compatible; MSIE 6.0; Windows NT 5.1';
	/**
	 *
	 * @param $hostname
	 * @param $username
	 * @param $password
	 * @param $ip (optional)
	 * @return unknown_type
	 */
	function __construct($hostname='',$username='',$password='',$ip=0)
	{
		if (!empty($hostname) && !empty($username) && !empty($password))
		{
$url = http://username:password@dynupdate.no-ip.com/nic/update?
hostname=mytest.testdomain.com&myip=1.2.3.4 An example basic, raw HTTP
header GET request GET
/nic/update?hostname=mytest.testdomain.com;
		}
	}
}
?>
http://username:password@dynupdate.no-ip.com/nic/update?
hostname=mytest.testdomain.com&myip=1.2.3.4 An example basic, raw HTTP
header GET request GET
/nic/update?hostname=mytest.testdomain.com&myip=1.2.3.4 HTTP/1.0 Host:
dynupdate.no-ip.com Authorization: Basic base64-encoded-auth-string
User-Agent: Bobs Update Client WindowsXP/1.2 bob@somedomain.com

base64-encoded-auth-string should be the base64 encoding of
username:password. Un-encoded strings are accepted as well. Important:
Setting the Agent When making an update it is important that your http
request include an HTTP User-Agent to help No-IP identify different
clients that access the system. Clients that do not supply a User-Agent
risk being blocked from the system. Your user agent should be in the
following format User-Agent: NameOfUpdateProgram/VersionNumber
maintainercontact@domain.com For example: User-Agent: Bobs Update Client
WindowsXP/1.2 bob@somedomain.com URI Parameters Field Description
username:password Required Username and password associated with the
hosts that are to be updated. No-IP uses an email address as the
username. Email addresses will be no longer than 50 characters. hostname
Required The hostname(s) (host.domain.com) or group(s) (group_name) to
be updated . If updating multiple hostnames or groups use a comma
sepearted list. hostname=host1.domain.com,group1,host2.domain.com.
Results are returned in the order you submitted them to the API per
line. myip Optional The IP address to which the host(s) will be set. If
no IP address is supplied the WAN address connecting to our system will
be used. Clients behind NAT, for example, would not need to supply an IP
address
