<?php
    /**
    * whiteGold - mini PHP Framework
    *
    * @package whiteGold
    * @author Farawe iLyas <faraweilyas@gmail.com>
    * @link http://faraweilyas.me
    *
    * Configuration for framework constants
    * Framework Constants that aren't defined
    */

	defined('DB_HOST') 			? NULL : define("DB_HOST", 				"localhost");
	defined('DB_USERNAME') 		? NULL : define("DB_USERNAME", 			"root");
	defined('DB_PASSWORD') 		? NULL : define("DB_PASSWORD", 			"");
	defined('DB_NAME') 			? NULL : define("DB_NAME", 				"");

	defined('MAIL_DRIVER') 		? NULL : define("MAIL_DRIVER", 			"smtp");
	defined('MAIL_HOST') 		? NULL : define("MAIL_HOST", 			"smtp.gmail.com");
	defined('MAIL_USERNAME') 	? NULL : define("MAIL_USERNAME", 		"");
	defined('MAIL_PASSWORD') 	? NULL : define("MAIL_PASSWORD", 		"");
	defined('MAIL_SMTPSECURE') 	? NULL : define("MAIL_SMTPSECURE", 		"ssl");
	defined('MAIL_PORT') 		? NULL : define("MAIL_PORT", 			"465");
	// defined('MAIL_SMTPSECURE') 	? NULL : define("MAIL_SMTPSECURE", 		"tls");
	// defined('MAIL_PORT') 		? NULL : define("MAIL_PORT", 			"587");
	defined('MAIL_EMAIL') 		? NULL : define("MAIL_EMAIL", 			MAIL_USERNAME);

	defined('LOG') 				? NULL : define('LOG', 					"");
	defined('UPLOAD') 			? NULL : define('UPLOAD', 				"");
	defined('SESSION') 			? NULL : define('SESSION', 				"");
	defined('COOKIE') 			? NULL : define('COOKIE', 				"");
	defined('TEMPLATE') 		? NULL : define('TEMPLATE', 			"");
	defined('VIEW') 			? NULL : define('VIEW', 				"");
