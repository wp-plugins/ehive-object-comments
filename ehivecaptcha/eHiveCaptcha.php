<?php

/*----------------------------------
 * eHiveCaptcha engine
 *----------------------------------*/

class eHiveCaptcha {
	// CONFIGURATION
	public $img_dir     = "images"; // Directory with captcha images
	
	public $good_images = 80; // Number of good images
	public $bad_images  = 31; // Number of bad images

	public $image_count = 6;  // Number of images to show
	public $good_count  = 3;  // Number of good images to display in the grid

	public $rnd_good 	= true;  // Randomize number of good images
	
	// $good_count becomes maximum number
	public $use_imagick = true; // Should use ImageMagick for image processing?
	public $use_gd      = true;  // Should use GD if ImageMagick is unavailable?

	public $max_check_count = 3; // Maximum number of checks per captcha, per user
	// 0 if unlimited
	
	public function __construct() {
		require_once('DAO/eHiveCaptchaDAO.php');
	}
	
	public static function getEHiveCaptcha($eHiveCaptchaText, $formId, $height, $width) {
		new eHiveCaptcha();
		return '<div id="eHiveCaptchaTitleTextDiv">
					<span id="eHiveCaptchaTitleText" ' . (isset($height) ? 'height="' . $height . '" ' : '') . (isset($width) ? 'width="' . $width . '">' : '>') .  $eHiveCaptchaText . '</span>
				</div>
				<div id="eHiveCaptchaContainerDiv">
					
					<div id="eHiveCaptchaDiv">
						<div id="eHiveCaptchaSpinner" style="display:none;">
							<img id="eHiveCaptchaImgSpinner" src="' . EHIVE_CAPTCHA_SPINNER_IMG . '" alt="Loading" />
						</div>
						<div class="eHiveCaptchaImageContainerDiv" id="eHiveCaptchaImageContainerDiv" ></div>
					</div>
				
					<div id="eHiveCaptchaHelpLinkDiv">
						<a href="http://en.wiki.ehive.com/wiki/EHive_image_puzzle" id="eHiveCaptchaHelpLinkAnchor" target="_blank">
							Help about our puzzle
						</a>
					</div>
					
					<div id="eHiveCaptchaRefreshCaptchaDiv">
						<div id="eHiveCaptchaRefreshCaptchaText">Get new puzzle:</div>
						<div id="eHiveCaptchaRefreshIcon" onClick="refreshCaptcha(' . $formId . ');">
							<img id="eHiveCaptchaRefreshStaticGif" align="left" src="' . EHIVE_CAPTCHA_REFRESH_IMG_STATIC . '" alt="refresh"/>
							<img id="eHiveCaptchaRefreshAnimatedGif" src="' . EHIVE_CAPTCHA_REFRESH_IMG_ANIMATED .'" alt="refresh"/>
						</div>
					</div>
					
					<span id="eHiveCaptchaPathToFile" style="display : none;">' . EHIVE_CAPTCHA_PATH_TO_FILE . '</span>
				</div>';
	} 
	
	// Generate images for the captcha
	public function generateCaptcha() {		
		eHiveCaptchaDAO::setCaptchaCheckCount(0);
		
		if ($this->rnd_good == false) {
			$good = $this->good_count;
		} else {
			$good = rand(1, $this->good_count);
		}
		
		eHiveCaptchaDAO::setCaptchaImagesGoodCount($good);
		
		$good_history = array();
		$bad_history = array();
		$images = array();

		// Generate an array of images
		for($i = 0; $i < $this->image_count; $i++) {
			
			if ($good > 0) { // "Good" images
				$is_good = true;
				$id = substr(md5(rand()), 0, 8);
				$img_num = rand(1, $this->good_images);
				
				while (in_array($img_num, $good_history)) {
					$img_num = rand(1, $this->good_images);
				}

				$good_history[] = $img_num;
				
			} else { // "Bad" images
				$is_good = false;
				$id = substr(md5(rand()), 0, 8);
				$img_num = rand(1, $this->bad_images);
				
				while (in_array($img_num, $bad_history)) {
					$img_num = rand(1, $this->bad_images);
				}

				$bad_history[] = $img_num;
			}

			// Append it to image array
			$images[] = array(
				"is_good" => $is_good,
				"filename" => $this->img_dir . "/" . ($is_good ? "good" : "bad") . "/" . $img_num . ".jpg",
				"id" => $id
			);
			$good--;
		}

		// Shuffle the array
		shuffle($images);
		
		
		// Set id's as keys
		foreach ($images as $key => $image) {
			$images[$image['id']] = $image;
			unset($images[$key]);
		}

		// Save image array into session
		if (eHiveCaptchaDAO::getCaptchaImages() != null) {
			eHiveCaptchaDAO::clearCaptchaImages();
		}
		
		eHiveCaptchaDAO::setCaptchaImages($images);
				
		// Print image ids to the browser
		$output = array();
		foreach ($images as $image) {
			$output[] = $image['id'];
		}
		print implode(",", $output);
	}

	// ImageMagick's processing
	public function getImageImagick($filename) {
		// Load the image
		$image = new Imagick($filename);

		// Randomize HSL and quality
		$h = rand(95, 105);
		$s = rand(80, 120);
		$l = rand(80, 120);
		$quality = rand(70,95);

		// Apply changes to image
		$image->setImageCompression(Imagick::COMPRESSION_JPEG);
		$image->setImageCompressionQuality($quality);
		$image->modulateImage($h, $s, $l);

		// Print to browser
		Header("Content-type: image/jpeg");
		print $image;
	}

	// PHP GD processing when Imagick is not available
	public function getImageGD($filename) {
		// Load the image
		$image = imagecreatefromjpeg($filename);

		// Randomize contrast, colorization and quality
		$contrast = rand(-10, 10);
		$r = rand(-10, 10);
		$g = rand(-10, 10);
		$b = rand(-10, 10);
		$quality = rand(70,95);

		// Apply changes to image
		imagefilter($image, IMG_FILTER_CONTRAST, $contrast);
		imagefilter($image, IMG_FILTER_COLORIZE, $r, $g, $b, 0);

		// Print to browser
		Header("Content-type: image/jpeg");
		imagejpeg($image, NULL, $quality);
		imagedestroy($image);
	}


	// Return an image based on random image ID
	public function getImage($id) {	
		$image_arr = eHiveCaptchaDAO::getCaptchaImages();
		
		if($image_arr[$id]) {
			$filename = $image_arr[$id]['filename'];

			// Pass the file to image processing or just display it
			if ($this->use_imagick && extension_loaded('imagick')) {
				$this->getImageImagick($filename);
			} else if ($this->use_gd && extension_loaded('gd')) {
				$this->getImageGD($filename);
			} else {
				Header("Content-type: image/jpeg");
				print file_get_contents($filename);
			}
		}
	}

	// Check image list
	public function check($list) {
		if (eHiveCaptchaDAO::getCaptchaCheckCount() == null){
			eHiveCaptchaDAO::setCaptchaCheckCount(1);
		} else {
			$check_count = eHiveCaptchaDAO::getCaptchaCheckCount();
			eHiveCaptchaDAO::setCaptchaCheckCount(++$check_count);
		} 
		
		if (eHiveCaptchaDAO::getCaptchaCheckCount() >= $this->max_check_count) {
			return "error_regenerate";
		}
		
		// Check if user submitted correct number of id's
		$check_list = explode(",", $list);
		
		$num_of_good_images = eHiveCaptchaDAO::getCaptchaImagesGoodCount();
		if (count(array_unique($check_list)) != $num_of_good_images){ 	
			return "error_not_enough";
		}

		// Check the id's
		foreach ($check_list as $check) {
			$image_arr = eHiveCaptchaDAO::getCaptchaImages(); 
			if (!$image_arr[$check]['is_good']){
				return "error_wrong";
			}
		}

		return "success";
	}
}

// Pick an action
if (isset($_GET['generate_captcha'])) {
	$ehCaptcha = new eHiveCaptcha();
	$ehCaptcha->generateCaptcha();
} else if (isset($_GET['get_image'])) {
	$ehCaptcha = new eHiveCaptcha();
	$ehCaptcha->getImage($_GET['get_image']);
} else if (isset($_GET['check'])) {
	$ehCaptcha = new eHiveCaptcha();
	echo $ehCaptcha->check($_GET['check']);
}
?>