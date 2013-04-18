<?php
/*
	Copyright (C) 2012 Vernon Systems Limited

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php 

$cssClass = isset($options['css_class']) ? $options['css_class'] : '';

if (!isset($eHiveApiErrorMessage)) {

	if ($options['commenting_enabled'] == 'on' && $commentsCollection->allowCommenting == true) {
?>
	<div class='ehive-object-comment' <?php echo $cssClass; ?> >
		<a id="add-ehive-object-comment-link" href="#add-ehive-object-comment-link">- Add Comment</a>
	
		<div id="ehive-object-comment-form-wrap" style="display: block;">
		    <form id="ehive-object-comment-form" action="<?php echo ehive_current_url() ?>" method="POST" name="ehive-object-comment-form">
		    	<fieldset>
			        <div class="ehive-object-comment-form-field">
			            <label for="ehive-object-comment-form-name-field">Name</label>
			            <input id="ehive-object-comment-form-name-field" name="ehive-object-comment-form-name-field" value="" type="text" placeholder="<?php echo $options['name_placeholder']?>">
			            <?php if (isset($options['name_required']) && $options['name_required'] == 'on'){?>
			            <span class="ehive-mandatory-field">*</span>
			        	<?php }?>
			        </div>
			        <?php if (isset($options['name_required']) && $options['name_required'] == 'on'){?>
			        <div id="ehive-object-comment-form-name-field-validation" class="ehive-validation-message" style="display:none;">
			        	<span>Please enter your name.</span>
			        </div>		 
			        <?php }?>    
			           
			        <div class="ehive-object-comment-form-field">
			            <label for="ehive-object-comment-form-email-field">eMail</label>
			            <input id="ehive-object-comment-form-email-field" name="ehive-object-comment-form-email-field" value="" type="email" placeholder="<?php echo $options['email_placeholder']?>">	
			            <?php if (isset($options['email_address_required']) && $options['email_address_required'] == 'on'){?>
			            <span class="ehive-mandatory-field">*</span>
			        	<?php }?>
			        </div>
			        <?php if (isset($options['email_address_required']) && $options['email_address_required'] == 'on'){?>
			        <div id="ehive-object-comment-form-email-field-validation" class="ehive-validation-message" style="display:none;">
			        	<span>Please enter your EMail address.</span>
			        </div>		 
			        <?php }?>
			        
			        <div id="ehive-object-comment-form-email-field-validation-error-message" class="ehive-validation-message" style="display:none;">
			        	<span>The email address you entered is invalid. Please check it and try again</span>
		            </div>   
		            
			        <div class="ehive-object-comment-form-field">
			            <label for="ehive-object-comment-form-comment-text-field">Comment</label>
			            <textarea id="ehive-object-comment-form-comment-text-field" name="ehive-object-comment-form-comment-text-field"  rows="5" cols="40" placeholder="<?php echo $options['comment_placeholder']?>"></textarea>
			            <span class="ehive-mandatory-field">*</span>
			        </div>
			        <div id="ehive-object-comment-form-comment-text-field-validation" class="ehive-validation-message" style="display:none;">
			        	<span>Please enter your comment.</span>
			        </div>	
			        <?php 
						if (isset($options['captcha_enabled']) && $options['captcha_enabled'] == 'on') {
							require_once(plugin_dir_path(__FILE__) . '../ehivecaptcha/DAO/eHiveCaptchaDAO.php');
							require_once(plugin_dir_path(__FILE__) . '../ehivecaptcha/eHiveCaptcha.php');
							
							$captchaText = $options['captcha_header_text']; //'Are you a human? Solve this puzzle to post your comment. <br/> Click on all the <strong>images of bees</strong>.';
							
							$captchaHtml  = '<div id="ehive-object-comment-captcha-field">';
							if (isset($options['image_border_colour_enabled']) && $options['image_border_colour_enabled'] == 'on'){
								$captchaHtml .= "<input id='ehive-captcha-image-border-color' type='hidden' value='{$options['image_border_colour']}'>";
							}
							if (isset($options['selected_image_border_colour_enabled']) && $options['selected_image_border_colour_enabled'] == 'on'){
								$captchaHtml .= "<input id='ehive-captcha-selected-image-border-color' type='hidden' value='{$options['selected_image_border_colour']}'>";
							}
							$captchaHtml .= eHiveCaptcha::getEhiveCaptcha($captchaText, true, "ehive-object-comment-form", $height='38px' ,$width='320px');
							$captchaHtml .= '<span class="ehive-mandatory-field">*</span>';
							$captchaHtml .= '<span id="ehive-object-comment-captcha-field-validation-error-message">' . $options['captcha_error_text'] . '</span>';
							$captchaHtml .= '</div>';
							
							echo $captchaHtml;
						}
					?>
			        <p class="ehive-object-comment-manditory-note">Required fields are marked *</p>	        
					<span id="ehive-object-comment-validation-error-message" style="display : none;">Error message</span>
			        <button id="ehive-object-comment-form-submit" name="ehive-object-comment-form-submit">Submit</button>
			        <input type="hidden" id="ehive-object-comment-form-submit" name="ehive-object-comment-form-submit" value="Submit">
			        <input type="hidden" value="<?php echo $object_record_id ?>" name="ehive_object_record_id">		        
		        </fieldset>
		    </form>
		</div>	     
	</div>
	<?php }
	
	if ($css_class == "") {
		echo '<div class="ehive-object-comment-list">';
	} else {
		echo '<div class="ehive-object-comment-list '.$css_class.'">';
	}
	 
	if (isset($commentsCollection->comments)) {
		$html = '';
		foreach ($commentsCollection->comments as $key => $comment)  {
	   		$html .= "<div class=\"ehive-comment-wrap\">
			    	<div class=\"ehive-comment\">
			        	<span class=\"ehive-comment-name\">{$comment->commentorName}</span>
			        	<span class=\"ehive-comment-when-posted\">{$comment->whenCreated}</span>
			        </div>
			        <p class=\"ehive-comment-text\">{$comment->commentText}</p>
			    </div>";
		}
		echo $html;
	}
} else { ?>
	<div class="ehive-object-comment">
		<p class='ehive-error-message ehive-object-comments-error'><?php echo $eHiveApiErrorMessage; ?></p>
	</div>
<?php } ?>
