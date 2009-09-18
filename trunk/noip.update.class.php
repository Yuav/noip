<?php
/**
 *
 * @author Jon Skarpeteig
 * @example new noip('foo.zapto.org','bar@hotmail.com','password','10.0.0.4');
 */

class noip {
	public $last_error;
	private $username, $password, $hostname;

	/*
	 Send max 1 update request each 30 minutes
	 */

	private $min_update_delay = 1800;

	/*Optional The IP address to which the host(s) will be set. If
	 no IP address is supplied the WAN address connecting to our system will
	 be used. Clients behind NAT, for example, would not need to supply an IP
	 address*/

	private $_staticip;

	/*
	 Setting the Agent When making an update it is important that your http
	 request include an HTTP User-Agent to help No-IP identify different
	 clients that access the system. Clients that do not supply a User-Agent
	 risk being blocked from the system. Your user agent should be in the
	 following format User-Agent: NameOfUpdateProgram/VersionNumber
	 maintainercontact@domain.com For example: User-Agent: Bobs Update Client
	 WindowsXP/1.2 bob@somedomain.com
	 */

	protected $_useragent = 'Jon Skarpeteig PHP Update Client/1.0 jon.skarpeteig@gmail.com';
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
		# Username and password associated with the hosts that are to be updated.
		# No-IP uses an email address as the username.
		# Email addresses will be no longer than 50 characters.
		$this->username = (!empty($username)) ? $username : $this->username;
		$this->password = (!empty($password)) ? $password : $this->password;

		# The hostname(s) (host.domain.com) or group(s) (group_name) to be updated .
		# If updating multiple hostnames or groups use a comma sepearted list.
		# hostname=host1.domain.com,group1,host2.domain.com.
		# Results are returned in the order you submitted them to the API per line.
		$this->hostname = (!empty($hostname)) ? $hostname : $this->hostname;

		$this->_staticip = (!empty($ip)) ? $ip : $this->_staticip;

		if (!$this->update())
		{
			throw new Exception('No-Ip update failed! '.$this->last_error);
		}
	}
	public function update()
	{
		$last_update = @file_get_contents(realpath(dirname(__FILE__)).'/last_update');
		if ((time()-$last_update) < $this->min_update_delay) {
			$this->last_response = 'Last update was done '.time()-$last_update.' seconds ago!
				Allow '.$this->min_update_delay.' seconds between regular updates'; 
			return false;
		}

		# create a new cURL resource
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_USERAGENT,$this->_useragent);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //return error codes

		# base64-encoded-auth-string should be the base64 encoding of
		# username:password. Un-encoded strings are accepted as well.
		curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password"); //base64 encoded

		//$url = "http://$username:$password@dynupdate.no-ip.com/nic/update?hostname=$this->hostname";
		$url = "http://dynupdate.no-ip.com/nic/update?hostname=$this->hostname";
		# ip only needed if you want to use another ip than the one seen by server when you connect
		$url = (empty($this->_staticip)) ? $url : $url."&myip=$this->_staticip";
		curl_setopt($ch,CURLOPT_URL,$url);

		$response = curl_exec($ch);

		# close cURL resource, and free up system resources
		curl_close($ch);

		$this->last_error = $response;

		# Store last update time, to avoid getting banned for hammering
		file_put_contents(realpath(dirname(__FILE__)).'/last_update',time());
		return ($response) ? $this->return_codes($response) : $response;
	}
	protected function return_codes($response)
	{
		switch ($response)
		{
			## ERROR ##
			case 'nohost':
				# Hostname supplied does not exist under specified account, client exit and require user to enter new login credentials before performing and additional request.
				$this->last_error = 'Hostname '.$this->hostname.' does not exist under specified account';
				return false;
				break;
			case 'badauth':
				# Invalid username password combination
				$this->last_error = 'Invalid username or password supplied!';
				return false;
				break;
			case 'badagent':
				# Client disabled. Client should exit and not perform any more updates without user intervention.
				return false;
				break;
			case '!donator':
				# An update request was sent including a feature that is not available to that particular user such as offline options.
				return false;
				break;
			case 'abuse':
				# Username is blocked due to abuse. Either for not following our update specifications or disabled due to violation of the No-IP terms of service. Our terms of service can be viewed at http://www.no-ip.com/legal/tos. Client should stop sending updates.
				return false;
				break;
			case '911':
				# A fatal error on noip side such as a database outage. Retry the update no sooner 30 minutes.
				return false;
				break;
			default:
				preg_match('/(.*?) ./s',$response,$match);
				switch ($match[1])
				{
					## Success ##
					case 'good':
						# DNS hostname update successful. Followed by a space and the IP address it was updated to.
						return true;
						break;
					case 'nochg':
						# IP address is current, no update performed. Followed by a space and the IP address that it is currently set to.
						return true;
						break;
					default:
						return false;
						break;
				}
				return false;
				break;
		}
	}
}
?>