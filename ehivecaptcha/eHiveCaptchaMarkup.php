<div id="eHiveCaptchaTitleTextDiv">
	<span id="eHiveCaptchaTitleText" height="' . $height . '" width="' . $width . '">' .  $eHiveCaptchaText . '</span>
</div>
<div id="eHiveCaptchaContainerDiv">

	<div id="eHiveCaptchaDiv">
		<div id="eHiveCaptchaImageContainerDiv" />
	</div>
	<div id="eHiveCaptchaHelpLinkDiv">
		<a href="http://en.wiki.ehive.com/wiki/EHive_image_puzzle" id="eHiveCaptchaHelpLinkAnchor" target="_blank">
			<span>Help about our puzzle</span>
		</a>
	</div>
	
	<div id="eHiveCaptchaRefreshCaptchaDiv">
		<span id="eHiveCaptchaRefreshCaptchaText">Get new puzzle:</span>
		<div id="eHiveCaptchaRefreshIcon" onClick="refreshCaptcha(' . $formId . ')">
			<img id="eHiveCaptchaRefreshStaticGif" align="left" src="' . EHIVE_CAPTCHA_REFRESH_IMG_STATIC . '" alt="refresh"/>
			<img id="eHiveCaptchaRefreshAnimatedGif" src="' . EHIVE_CAPTCHA_REFRESH_IMG_ANIMATED .'" alt="refresh"/>
		</div>
		
	</div>
	
	<div id="eHiveCaptchaSpinner" style="display:none;">
		<img id="eHiveCaptchaImgSpinner" src="' . EHIVE_CAPTCHA_SPINNER_IMG . '" alt="Loading" />
	</div>
	
	<input type="hidden" id="eHiveCaptchaImagesSelected" name="eHiveCaptchaImagesSelected" value=""/>
	<span id="eHiveCaptchaPathToFile" style="display : none;">' . EHIVE_CAPTCHA_PATH_TO_FILE . '</span>
</div>