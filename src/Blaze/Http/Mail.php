<?php
	namespace Blaze\Http;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	/**
	* whiteGold - mini PHP Framework
	*
	* @package whiteGold
	* @author Farawe iLyas <faraweilyas@gmail.com>
	* @link http://faraweilyas.me
	*
	* Mail Class
	*/
	class Mail
	{
		// Stores mail error message
		public static $errorMessage;

		// Stores mail result
		public static $result;

		/**
		* Sends Plain Email
		* @param \stdClass $emailBody
		* @param \stdClass $to
		* @param \stdClass $from
		* @param \stdClass $replyTo
		* @param \stdClass $SMTPConfig
		* @return bool
		*/
		public static function sendEmail (\stdClass $emailBody, \stdClass $to, \stdClass $from, \stdClass $replyTo, $SMTPConfig='') : bool
		{
			$mail = new PHPMailer;
			static::setSMTPConfig($mail, $SMTPConfig);
		    $mail->Subject 	= $emailBody->subject;
			$mail->Body 	= $emailBody->messageBody;
		    $mail->addAddress($to->email, $to->name);
		    $mail->setFrom($from->email, $from->name);
			$mail->addReplyTo($replyTo->email, $replyTo->name);
			static::$result 		= $mail->send();
			static::$errorMessage 	= $mail->ErrorInfo;
		    return static::$result ? TRUE : FALSE;
		}

		/**
		* Sends Html Email
		* @param \stdClass $emailBody
		* @param \stdClass $to
		* @param \stdClass $from
		* @param \stdClass $replyTo
		* @param \stdClass $SMTPConfig
		* @return bool
		*/
		public static function sendHtmlEmail (\stdClass $emailBody, \stdClass $to, \stdClass $from, \stdClass $replyTo, $SMTPConfig='') : bool
		{
			$mail = new PHPMailer;
			static::setSMTPConfig($mail, $SMTPConfig);
		    $mail->Subject 	= $emailBody->subject;
		    $mail->MsgHTML($emailBody->messageBody);
		    $mail->addAddress($to->email, $to->name);
		    $mail->setFrom($from->email, $from->name);
			$mail->addReplyTo($replyTo->email, $replyTo->name);
			static::$result 		= $mail->send();
			static::$errorMessage 	= $mail->ErrorInfo;
		    return static::$result ? TRUE : FALSE;
		}

		/**
		* Sends Html Email with Attachment
		* @param \stdClass $emailBody
		* @param \stdClass $to
		* @param \stdClass $from
		* @param \stdClass $replyTo
		* @param \stdClass $attachment
		* @param \stdClass $SMTPConfig
		* @return bool
		*/
		public static function sendWithAttachment (\stdClass $emailBody, \stdClass $to, \stdClass $from, \stdClass $replyTo, \stdClass $attachment, $SMTPConfig='') : bool
		{
			$mail = new PHPMailer;
			static::setSMTPConfig($mail, $SMTPConfig);
		    $mail->Subject 	= $emailBody->subject;
		    $mail->MsgHTML($emailBody->messageBody);
		    $mail->addAddress($to->email, $to->name);
		    $mail->setFrom($from->email, $from->name);
			$mail->addReplyTo($replyTo->email, $replyTo->name);
			$mail->addAttachment($attachment->fileName, $attachment->newFileName);
			static::$result 		= $mail->send();
			static::$errorMessage 	= $mail->ErrorInfo;
		    return static::$result ? TRUE : FALSE;
		}
		
		/**
		* Set STMP Server Config
		* @param PHPMailer $mail
		* @param \stdClass $SMTPConfig
		* @return void
		*/
		public static function setSMTPConfig (PHPMailer $mail, $SMTPConfig='')
		{
			if (is_object($SMTPConfig)) static::configure($mail, $SMTPConfig);
		}

		/**
		* Configures STMP Server
		* @param PHPMailer $mail
		* @param \stdClass $configurations
		* @return void
		*/
		public static function configure (PHPMailer $mail, \stdClass $configurations)
		{
			$mail->isSMTP();
			$mail->SMTPAuth 	= true;
			$mail->Host 		= $configurations->host 		?? '';
			$mail->Username 	= $configurations->username 	?? '';
			$mail->Password 	= $configurations->password 	?? '';
			$mail->SMTPSecure 	= $configurations->SMTPSecure 	?? '';
			$mail->Port 		= $configurations->port 		?? '';
		}
		
		/**
		* Set SMTP configuration for email sending.
		* @param string $host
		* @param string $username
		* @param string $password
		* @param string $SMTPSecure
		* @param int $port
		* @return \stdClass
		*/
		final public static function setSMTP (string $host, string $username, string $password, string $SMTPSecure, int $port) : \stdClass
		{
			return (object) $SMTPConfig = [
				'host' 			=> $host,
				'username' 		=> $username,
				'password' 		=> $password,
				'SMTPSecure' 	=> $SMTPSecure,
				'port' 			=> $port
			];
		}
		
		/**
		* Sets email parameters and cast it into an object.
		* @param string $email
		* @param string $emailName
		* @return \stdClass
		*/
		final public static function setEmail (string $email, string $emailName="") : \stdClass
		{
			return (object) ['email' => $email, 'name' => $emailName];
		}

		/**
		* Sets email body parameters and cast it into an object.
		* @param string $subject
		* @param string $messageBody
		* @return \stdClass
		*/
		final public static function setBody (string $subject, string $messageBody) : \stdClass
		{
			return (object) ['subject' => $subject, 'messageBody' => $messageBody];
		}

		/**
		* Sets attachment parameters and cast it into an object.
		* @param string $file
		* @param string $fileName
		* @return \stdClass
		*/
		final public static function setAttachment (string $fileName, string $newFileName="") : \stdClass
		{
			return (object) ['fileName' => $fileName, 'newFileName' => $newFileName];
		}

		/**
		* A test to show an example of how it works.
		*/
		public static function testExample ()
		{
            $SMTPConfig	= Mail::setSMTP(
            	getConstant("MAIL_HOST"),
            	getConstant("MAIL_USERNAME"),
            	getConstant("MAIL_PASSWORD"),
            	getConstant("MAIL_SMTPSECURE"),
            	(int) getConstant("MAIL_PORT")
           	);
            $to         = Mail::setEmail(getConstant("MAIL_USERNAME"), "To Tester");
            $from       = Mail::setEmail(getConstant("MAIL_USERNAME"), "From Tester");
            $emailBody	= Mail::setBody("Message Subject", "Message Body");
            // var_dump(Mail::sendEmail($emailBody, $to, $from, $from, $SMTPConfig));
            // var_dump(static::$result, static::$errorMessage);
            // var_dump(Mail::sendHtmlEmail($emailBody, $to, $from, $from, $SMTPConfig));
            // $attachment = Mail::setAttachment("currentFileName.extension", "newFileName.extension");             
            // var_dump(Mail::sendWithAttachment($emailBody, $to, $from, $from, $attachment, $SMTPConfig));			
		}
	}