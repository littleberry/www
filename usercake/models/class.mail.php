<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/
//let's use the PEAR PHP mail module
$location_id = gethostname();
$catPattern = 'cathlenes-MacBook-Pro.local';
$muppetPattern = 'FORA';
switch ($location_id) { 
        case $catPattern : 
            require_once("/Applications/MAMP/bin/php/php5.4.10/lib/php/Mail.php");
			break; 
        case $muppetPattern:
        	
        	require_once("c:\wamp\bin\php\php-5.4.20-Win32-VC9-x86\PEAR\pear\Mail\smtp.php");
			//require_once("c:\wamp\bin\php\php-5.4.20-Win32-VC9-x86\PEAR\pear\Mail\Mail.php");
        	require_once("c:\wamp\bin\php\php-5.4.20-Win32-VC9-x86\PEAR\pear\Mail\Mail.php");
			break; 
    } 

class userCakeMail {
	//UserCake uses a text based system with hooks to replace various strs in txt email templates
	public $contents = NULL;
	
	//Function used for replacing hooks in our templates
	public function newTemplateMsg($template,$additionalHooks)
	{
		global $mail_templates_dir,$debug_mode;
		
		$this->contents = file_get_contents($mail_templates_dir.$template);
		
		//Check to see we can access the file / it has some contents
		if(!$this->contents || empty($this->contents))
		{
			return false;
		}
		else
		{
			//Replace default hooks
			$this->contents = replaceDefaultHook($this->contents);
			
			//Replace defined / custom hooks
			$this->contents = str_replace($additionalHooks["searchStrs"],$additionalHooks["subjectStrs"],$this->contents);
			
			return true;
		}
	}
	
	public function sendMail($email,$subject,$msg = NULL)
	{
		global $websiteName,$emailAddress;
		
		
		$from = "admin@time_tracker.com";
		$to = "Cathy <catsbap@gmail.com>";
		//$subject = "Hi!";
		$body = "Hi,\n\nHow are you?";
 
 		$host = "ssl://smtp.gmail.com";
 		$username = "catsbap@gmail.com";
 		$password = "bapot3844";
 		$port = "465";

		$smtp = Mail::factory('smtp',
		array (
		'host' => $host,
		'port' => $port,
		'auth' => true,
		'username' => $username,
		'password' => $password));
		
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/plain; charset=iso-8859-1\r\n";
		$header .= "From: ". $websiteName . " <" . $emailAddress . ">\r\n";
		$headers = array (
		'From' => $from,
   'To' => $email,
   'Subject' => $subject);
		
		//Check to see if we sending a template email.
		if($msg == NULL)
			$msg = $this->contents; 
		
		$message = $msg;
		
		
		$message = wordwrap($message, 70);
		error_log("sending message with the following options: email: " . $email . " headers: " . $headers . " message: " . $message);
		$mail = $smtp->send($email,$headers,$message);
		return $mail;
		//return mail($email,$subject,$message,$header);
	}
}

?>