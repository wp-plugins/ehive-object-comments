var eHiveCaptcha;

function submitCommentForm () {
	startSpinner();
	jQuery("#ehive-object-comment-form").ajaxSubmit({
	    dataType:"text",	
	    success:function(responseText, statusText) {
	    	location.reload();
	    }
	});		
}

jQuery(document).ready(function() {
	
	var s = true;
	
	jQuery(".eHiveCaptchaImageDiv").click(function(){
		jQuery('#ehive-object-comment-captcha-field-validation-error-message').hide(200);
	});
	
	jQuery('#ehive-object-comment-form-wrap').css('display', 'none');
	jQuery('#add-ehive-object-comment-link').text('+ Add Comment');
	
	jQuery('#add-ehive-object-comment-link').click(function () {

        if (jQuery('#ehive-object-comment-form-wrap').css('display') == 'none') {
        	jQuery('#add-ehive-object-comment-link').text('- Add Comment');
        	jQuery('#ehive-object-comment-form-wrap').fadeIn(600);
        	
        	var imageBorderColor = '';
        	var selectedImageBorderColor = '';
        	
        	if (jQuery('#ehive-captcha-image-border-color').length > 0) {
        		imageBorderColor = jQuery('#ehive-captcha-image-border-color').val();
        	}
        	
        	if (jQuery('#ehive-captcha-selected-image-border-color').length > 0) {
        		selectedImageBorderColor = jQuery('#ehive-captcha-selected-image-border-color').val();
        	}
        	
        	eHiveCaptcha = new EHiveCaptcha();
        	eHiveCaptcha.initialize("ehive-object-comment-form-wrap", imageBorderColor, selectedImageBorderColor);
        	
        } else {
        	jQuery('#add-ehive-object-comment-link').text('+ Add Comment');
        	jQuery('#ehive-object-comment-form-wrap').fadeOut(600);    		
        }
        return false;
    });	
	
	
	jQuery("#ehive-object-comment-form-submit").click(function () {
		s = true;
		
		if (jQuery('#ehive-object-comment-form-name-field-validation').length){
			if (jQuery("#ehive-object-comment-form-name-field").val().trim().length < 1) {
				jQuery("#ehive-object-comment-form-name-field-validation").show(200);
				s = false;
			} else {
				jQuery("#ehive-object-comment-form-name-field-validation").hide(200);
			}
		}
		
		if (jQuery('#ehive-object-comment-form-email-field').val().length > 0) {
			
			var emailAddress = jQuery("#ehive-object-comment-form-email-field").val();
			var regEx = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			
			if (regEx.test(emailAddress) == true) {
				jQuery("#ehive-object-comment-form-email-field-validation-error-message").hide(200);
			} else {
				jQuery("#ehive-object-comment-form-email-field-validation-error-message").show(200);
				s = false;
			}
		}

		if (jQuery('#ehive-object-comment-form-email-field-validation').length && jQuery('#ehive-object-comment-form-email-field').val().trim().length > 0 == false) {
			jQuery("#ehive-object-comment-form-email-field-validation").show(200);
			s = false;
		} else {
			jQuery("#ehive-object-comment-form-email-field-validation").hide(200);
		}
				
		if (jQuery("#ehive-object-comment-form-comment-text-field").val().trim().length < 1) {
			jQuery("#ehive-object-comment-form-comment-text-field-validation").show(200);
			s = false;
		} else {
			jQuery("#ehive-object-comment-form-comment-text-field-validation").hide(200);
		}
	  	
		if (jQuery("#eHiveCaptchaContainerDiv").length && s) {
			startSpinner();
			jQuery.ajax({
				url: jQuery("#eHiveCaptchaPathToFile").text() + '?check=' + jQuery('#eHiveCaptchaImagesSelected').val(),
				dataType: 'text',
				method: 'get',
				success: function(responseText, statusText) {
					if (responseText == "success") {
						jQuery('#ehive-object-comment-captcha-field-validation-error-message').hide(200);
						submitCommentForm();
					} else if (responseText == "error_regenerate") {
						eHiveCaptcha.initialize("ehive-object-comment-form-wrap");
						stopSpinner();
					} else {
						jQuery('#ehive-object-comment-captcha-field-validation-error-message').show(200);
						stopSpinner();
					}
				}
			});
		} else {
			if(s) {
				submitCommentForm();
			}
		}
		return false;
	});			
});		
