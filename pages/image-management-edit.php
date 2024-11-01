<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
if(!is_numeric($did) || $did == 0 ) { 
	die('<p>Are you sure you want to do this?</p>'); 
}

$sep_errors = array();
$sep_success = '';
$sep_error_found = false;

$form = array(
	'sep_title' => '',
	'sep_text' => '',
	'sep_group' => '',
	'sep_style1' => '',
	'sep_style2' => '',
	'sep_style3' => '',
	'sep_status' => '',
	'sep_start' => '',
	'sep_end' => ''
);
	
$result = sepopups_cls_dbquery::sepopups_count($did);
if ($result != '1') {
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'simple-exit-popup'); ?></strong></p></div><?php
}
else {
	$data = array();
	$data = sepopups_cls_dbquery::sepopups_select_byid($did);
	
	$form = array(
		'sep_title' => $data['sep_title'],
		'sep_text' => $data['sep_text'],
		'sep_group' => $data['sep_group'],
		'sep_style1' => $data['sep_style1'],
		'sep_style2' => $data['sep_style2'],
		'sep_style3' => $data['sep_style3'],
		'sep_status' => $data['sep_status'],
		'sep_start' => $data['sep_start'],
		'sep_end' => $data['sep_end'],
		'sep_id' => $data['sep_id']
	);
}

if (isset($_POST['sep_form_submit']) && sanitize_text_field($_POST['sep_form_submit']) == 'yes') {
	check_admin_referer('sep_form_edit');
	
	$form['sep_title'] = isset($_POST['sep_title']) ? sanitize_text_field($_POST['sep_title']) : '';
	if ($form['sep_title'] == '') {
		$sep_errors[] = __('Please enter popup box title.', 'simple-exit-popup');
		$sep_error_found = true;
	}

	$form['sep_text'] = isset($_POST['sep_text']) ? wp_filter_post_kses($_POST['sep_text']) : '';
	if ($form['sep_title'] == '') {
		$sep_errors[] = __('Please enter popup content.', 'simple-exit-popup');
		$sep_error_found = true;
	}
	
	$form['sep_group'] = isset($_POST['sep_group']) ? sanitize_text_field($_POST['sep_group']) : '';
	if ($form['sep_group'] == '') {
		$form['sep_group'] = isset($_POST['sep_group_txt']) ? sanitize_text_field($_POST['sep_group_txt']) : '';
	}
	if ($form['sep_group'] == '') {
		$sep_errors[] = __('Please select group for this popup box.', 'simple-exit-popup');
		$sep_error_found = true;
	}
	
	$form['sep_style1'] = isset($_POST['sep_style1']) ? sanitize_text_field($_POST['sep_style1']) : '';
	$form['sep_style2'] = isset($_POST['sep_style2']) ? sanitize_text_field($_POST['sep_style2']) : '';
	$form['sep_style3'] = isset($_POST['sep_style3']) ? wp_filter_post_kses($_POST['sep_style3']) : '';
	$form['sep_status'] = isset($_POST['sep_status']) ? sanitize_text_field($_POST['sep_status']) : 'Yes';
	$form['sep_start'] = isset($_POST['sep_start']) ? sanitize_text_field($_POST['sep_start']) : '0000-00-00';
	$form['sep_end'] = isset($_POST['sep_end']) ? sanitize_text_field($_POST['sep_end']) : '9999-12-31';
	
	if ($sep_error_found == FALSE) {	
		$status = sepopups_cls_dbquery::sepopups_update($form);
		if($status == 'update') {
			$sep_success = __('Details was successfully updated.', 'simple-exit-popup');
		}
		else {
			$sep_errors[] = __('Oops, something went wrong. try again.', 'simple-exit-popup');
			$sep_error_found = true;
		}
	}
}

if ($sep_error_found == true && isset($sep_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $sep_errors[0]; ?></strong></p></div><?php
}

if ($sep_error_found == false && strlen($sep_success) > 0) {
	?><div class="updated fade"><p><strong><?php echo $sep_success; ?>
	<a href="<?php echo SEPOPUP_ADMIN_URL; ?>"><?php _e('Click here', 'simple-exit-popup'); ?></a> <?php _e('to view the details', 'simple-exit-popup'); ?>
	</strong></p></div><?php
}
?>
<div class="form-wrap">
	<h1 class="wp-heading-inline"><?php _e('Edit', 'simple-exit-popup'); ?></h1>
	<form name="sep_form" method="post" action="#" onsubmit="return _sepopups_submit()"  >
      
	  <label><strong><?php _e('Title', 'simple-exit-popup'); ?></strong></label>
      <input name="sep_title" type="text" id="sep_title" value="<?php echo $form['sep_title']; ?>" size="60" maxlength="1024" />
      <p><?php _e('Please enter popup box title.', 'simple-exit-popup'); ?></p>
	  
      <label><strong><?php _e('Popup content', 'simple-exit-popup'); ?></strong></label>
      <?php 
	  wp_editor(stripslashes($form['sep_text']), "sep_text"); 
	  ?>
      <p><?php _e('Please enter popup content.', 'simple-exit-popup'); ?></p>
	   
	  <label><strong><?php _e('Inline CSS style', 'simple-exit-popup'); ?></strong></label>
      <input name="sep_style1" type="text" id="sep_style1" value="<?php echo stripslashes(htmlspecialchars($form['sep_style1'])); ?>" maxlength="1024" size="60" />
      <p><?php _e('Please enter css inline style info for popup window.', 'simple-exit-popup'); ?> (Example: background: white;padding: 10px;border: 10px solid #666666;)</p>
	  
	  <label><strong><?php _e('CSS class name', 'simple-exit-popup'); ?></strong></label>
      <input name="sep_style2" type="text" id="sep_style2" value="<?php echo stripslashes(htmlspecialchars($form['sep_style2'])); ?>" maxlength="1024" size="60" />
      <p><?php _e('Please enter css class info for popup window.', 'simple-exit-popup'); ?> (Example: site-content, entry-content)</p>
	  
	  <label><strong><?php _e('Close button inline style', 'simple-exit-popup'); ?></strong></label>
      <input name="sep_style3" type="text" id="sep_style3" value="<?php echo stripslashes(htmlspecialchars($form['sep_style3'])); ?>" maxlength="1024" size="60" />
      <p><?php _e('Please enter css inline style info for close buttom.', 'simple-exit-popup'); ?></p>
	  
      <label><strong><?php _e('Group', 'simple-exit-popup'); ?></strong></label>
		<select name="sep_group" id="sep_group">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$selected = "";
			$groups = array();
			$groups = sepopups_cls_dbquery::sepopups_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					if(strtoupper($form['sep_group']) == strtoupper($group["sep_group"])) { 
						$selected = "selected"; 
					}
					?>
					<option value="<?php echo stripslashes($group["sep_group"]); ?>" <?php echo $selected; ?>>
						<?php echo stripslashes($group["sep_group"]); ?>
					</option>
					<?php
					$selected = "";
				}
			}
			?>
		</select>
		(or) 
	   	<input name="sep_group_txt" type="text" id="sep_group_txt" value="" maxlength="15" onkeyup="return _sepopups_numericandtext(document.sep_form.sep_group_txt)" />
      <p><?php _e('Please select group for this popup box.', 'simple-exit-popup'); ?></p>
	  
	  <label><strong><?php _e('Display status', 'simple-exit-popup'); ?></strong></label>
      <select name="sep_status" id="sep_status">
        <option value='Yes' <?php if($form['sep_status'] == 'Yes') { echo 'selected' ; } ?>>Yes</option>
        <option value='No' <?php if($form['sep_status'] == 'No') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Please select display status of this popup box.', 'simple-exit-popup'); ?></p>
	  
	  <label><strong><?php _e('Start date', 'simple-exit-popup'); ?></strong></label>
      <input name="sep_start" type="text" id="sep_start" value="<?php echo $form['sep_start']; ?>" maxlength="10" />
      <p><?php _e('Please enter display start date of this popup box, format YYYY-MM-DD.', 'simple-exit-popup'); ?></p>
	  
	  <label><strong><?php _e('End date', 'simple-exit-popup'); ?></strong></label>
      <input name="sep_end" type="text" id="sep_end" value="<?php echo $form['sep_end']; ?>" maxlength="10" />
      <p><?php _e('Please enter display end date of this popup box, format YYYY-MM-DD.', 'simple-exit-popup'); ?></p> 
	  
      <input name="sep_id" id="sep_id" type="hidden" value="<?php echo $form['sep_id']; ?>">
      <input type="hidden" name="sep_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'simple-exit-popup'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_sepopups_redirect()" value="<?php _e('Cancel', 'simple-exit-popup'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_sepopups_help()" value="<?php _e('Help', 'simple-exit-popup'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('sep_form_edit'); ?>
    </form>
</div>
</div>