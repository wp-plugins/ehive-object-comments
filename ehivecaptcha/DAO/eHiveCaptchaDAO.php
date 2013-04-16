<?php

/*----------------------------------
 * eHiveCaptcha WordPress DAO
 *----------------------------------*/

if(defined('EHIVE_CAPTCHA_DS') == false) define('EHIVE_CAPTCHA_DS', DIRECTORY_SEPARATOR); 

if(defined('EHIVE_CAPTCHA_PATH_BASE') == false) define('EHIVE_CAPTCHA_PATH_BASE', realpath("."));

if (defined('EHIVE_CAPTCHA_ICON_BASE_DIR') == false){
	$iconBaseDir = str_replace(EHIVE_CAPTCHA_PATH_BASE, '', dirname(__FILE__).'/images/icons');
	$iconBaseDir = str_replace('/DAO', '', $iconBaseDir);
	define('EHIVE_CAPTCHA_ICON_BASE_DIR', $iconBaseDir);
}

if (defined('EHIVE_CAPTCHA_SPINNER_IMG') == false) define('EHIVE_CAPTCHA_SPINNER_IMG', EHIVE_CAPTCHA_ICON_BASE_DIR.'/spinner2.gif');

if (defined('EHIVE_CAPTCHA_REFRESH_IMG_STATIC') == false) define('EHIVE_CAPTCHA_REFRESH_IMG_STATIC', EHIVE_CAPTCHA_ICON_BASE_DIR.'/refreshStatic.jpg');

if (defined('EHIVE_CAPTCHA_REFRESH_IMG_ANIMATED') == false) define('EHIVE_CAPTCHA_REFRESH_IMG_ANIMATED', EHIVE_CAPTCHA_ICON_BASE_DIR.'/refreshAnimated.gif');

if (defined('EHIVE_CAPTCHA_PATH_TO_FILE') == false) {
	$baseDir = str_replace(EHIVE_CAPTCHA_PATH_BASE, '', dirname(__FILE__));
	$baseDir = str_replace('/DAO', '', $baseDir);
	define('EHIVE_CAPTCHA_PATH_TO_FILE', $baseDir.'/eHiveCaptcha.php');
}

if (session_id() == '') {
	if (!session_start()) {
		throw "Session was not successfully initialised.";
	}
}

class eHiveCaptchaDAO {
	
	public static function getCaptchaCheckCount(){
		return array_key_exists('captcha_check_count', $_SESSION) ? $_SESSION['captcha_check_count'] : null;
	}
	public static function setCaptchaCheckCount($value) {
		$_SESSION['captcha_check_count'] = $value;
	}
	public static function clearCaptchaCheckCount() {
		unset($_SESSION['captcha_check_count']);
	}

	
	
	public static function getCaptchaImagesGoodCount() {
		return array_key_exists('captcha_images_good_count', $_SESSION) ? $_SESSION['captcha_images_good_count'] : null;
	}
	public static function setCaptchaImagesGoodCount($value) {
		$_SESSION['captcha_images_good_count'] = $value;
	}
	public static function clearCaptchaImagesGoodCount() {
		unset($_SESSION['captcha_images_good_count']);
	}
	
	
	
	public static function getCaptchaImages() {
		return array_key_exists('captcha_images', $_SESSION) ? $_SESSION['captcha_images'] : null;
	}
	public static function setCaptchaImages($value) {
		$_SESSION['captcha_images'] = $value;
	}
	public static function clearCaptchaImages() {
		unset($_SESSION['captcha_images']);
	}
}
?>