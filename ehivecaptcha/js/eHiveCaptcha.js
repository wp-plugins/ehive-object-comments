/*---------------------------------------
 * eHiveCaptcha frontend
 *---------------------------------------
 * A novel way to tell
 * if you are a human ;)
 *---------------------------------------*/

function startSpinner() {
	jQuery("#eHiveCaptchaSpinner").show();
}

function stopSpinner() {
	jQuery("#eHiveCaptchaSpinner").hide();
}

function showRefreshAnimated() {
	jQuery("#eHiveCaptchaRefreshStaticGif").hide();
	jQuery("#eHiveCaptchaRefreshAnimatedGif").show();
}

function hideRefreshAnimated() {
	jQuery("#eHiveCaptchaRefreshAnimatedGif").hide();
	jQuery("#eHiveCaptchaRefreshStaticGif").show();
}

function refreshCaptcha(form) {
	showRefreshAnimated();
	
	jQuery("#eHiveCaptchaImageContainerDiv").fadeOut(100);
	
	var eHiveCaptcha = new EHiveCaptcha();
	eHiveCaptcha.initialize("eHiveAddObjectCommentEditor");
}

var container;
var ehCaptchaFile;

EHiveCaptcha = function(){}; 

EHiveCaptcha.prototype = {

	// Create new instance of captcha class
	initialize : function(commentForm, imageBorderColor, selectedImageBorderColor) {
		this.container = jQuery("#eHiveCaptchaImageContainerDiv").get(0);
		this.ehCaptchaFile = jQuery("#eHiveCaptchaPathToFile").text();
		
		// Inject a hidden input with captcha answer into the form
		if(jQuery("#eHiveCaptchaImagesSelected").length > 0) {
		
		} else {
			this.answer = jQuery("<input/>", {
	            "type" : "hidden",
	            "name" : "eHiveCaptchaImagesSelected",
	            "id" : "eHiveCaptchaImagesSelected"
	        }).appendTo(jQuery("#"+commentForm));
		}
		
		if(imageBorderColor == null || imageBorderColor.length <= 0) {
			imageBorderColor = "#f3f3f3";
		}
		
		if(selectedImageBorderColor == null || selectedImageBorderColor.length <= 0) {
			selectedImageBorderColor = "#666666";
		}
		this.requestCaptcha(imageBorderColor, selectedImageBorderColor);
	},
	
	// Create new captcha
	requestCaptcha : function(imageBorderColor, selectedImageBorderColor) {
		jQuery.ajax({
			url : this.ehCaptchaFile + "?generate_captcha",
			method : "get",
			cache: false,
			success : function(responseText, statusText) {
				
				jQuery("#eHiveCaptchaImageContainerDiv").empty();
				var imageList = responseText.split(",");
				
				imageList.forEach(function(imageId) {
					var container = jQuery("#eHiveCaptchaImageContainerDiv").get(0);
					var ehCaptchaFile = jQuery("#eHiveCaptchaPathToFile").text();
					
					var captchaImageDiv = jQuery("<div/>", {
						"class" : "eHiveCaptchaImageDiv",
						"imageId" : imageId})
						.appendTo(container).click(function() {
							var image_array = [];
						
							jQuery(this).toggleClass('selected');
							
							jQuery.find("div.selected").forEach(function(div) {
								image_array.push(jQuery(div).attr("imageId"));
							});
					
							if (jQuery(this).hasClass('selected')) {
								jQuery(this).find('img').css({'border-color' : selectedImageBorderColor});
							} else {
								jQuery(this).find('img').css({'border-color' : imageBorderColor});
							}
							
							var answer = image_array.join(",");
							
							jQuery("#eHiveCaptchaImagesSelected").val("");
							jQuery("#eHiveCaptchaImagesSelected").val(answer);
							
						});
					
					jQuery("<img/>", {
							"src" : ehCaptchaFile + "?get_image=" + imageId,
							"class" : "eHiveCaptchaImage",
						}).css({"height" : "75px",
								"width"  : "75px",
								"border" : "5px solid " + imageBorderColor}).appendTo(captchaImageDiv);
					
				});
				
				jQuery('.eHiveCaptchaImageContainerDiv').fadeIn(600, function() {
					hideRefreshAnimated();
				});
			}
		});
	}
};
