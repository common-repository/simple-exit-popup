<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if (isset($_POST['frm_sep_display']) && sanitize_text_field($_POST['frm_sep_display']) == 'yes') {
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	if(!is_numeric($did)) { 
		die('<p>Are you sure you want to do this?</p>'); 
	}
	
	$success_msg = false;
	$result = sepopups_cls_dbquery::sepopups_count($did);

	if ($result != '1') {
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'simple-exit-popup'); ?></strong></p></div><?php
	}
	else {
		if (isset($_GET['ac']) && sanitize_text_field($_GET['ac']) == 'del' && isset($_GET['did']) && sanitize_text_field($_GET['did']) != '') {
			check_admin_referer('sep_form_show');
			sepopups_cls_dbquery::sepopups_delete($did);
			$success_msg = true;
		}
	}
	
	if ($success_msg == true) {
		?><div class="updated fade"><p><strong><?php _e('Selected record was successfully deleted.', 'simple-exit-popup'); ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
    <h2><?php _e('Simple exit popup', 'simple-exit-popup'); ?>
	<a class="add-new-h2" href="<?php echo SEPOPUP_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'simple-exit-popup'); ?></a></h2>
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = sepopups_cls_dbquery::sepopups_select();
	?>
	<form name="frm_sep_display" method="post">
      <table class="widefat" cellspacing="0">
        <thead>
          <tr>
		  	<th><?php _e('ID', 'simple-exit-popup'); ?></th>
			<th><?php _e('Title', 'simple-exit-popup'); ?></th>
			<th><?php _e('Group', 'simple-exit-popup'); ?></th>
			<th><?php _e('Status', 'simple-exit-popup'); ?></th>
			<th><?php _e('Start Date', 'simple-exit-popup'); ?></th>
			<th><?php _e('End Date', 'simple-exit-popup'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th><?php _e('ID', 'simple-exit-popup'); ?></th>
			<th><?php _e('Title', 'simple-exit-popup'); ?></th>
			<th><?php _e('Group', 'simple-exit-popup'); ?></th>
			<th><?php _e('Status', 'simple-exit-popup'); ?></th>
			<th><?php _e('Start Date', 'simple-exit-popup'); ?></th>
			<th><?php _e('End Date', 'simple-exit-popup'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 ) {
			foreach ($myData as $data) {
				?>
				<tr class="<?php if ($i&1) { echo ''; } else { echo 'alternate'; }?>">
					<td><?php echo $data['sep_id']; ?></td>
					<td>
					<?php echo $data['sep_title']; ?>
					<div class="row-actions">
					<span class="edit">
						<a title="Edit" href="<?php echo SEPOPUP_ADMIN_URL; ?>&ac=edit&amp;did=<?php echo $data['sep_id']; ?>">
							<?php esc_html_e('Edit', 'simple-exit-popup'); ?>
						</a> | 
					</span>
					<span class="trash">
						<a onClick="javascript:_sepopups_delete('<?php echo $data['sep_id']; ?>')" href="javascript:void(0);">
							<?php esc_html_e('Delete', 'simple-exit-popup'); ?>
						</a>
					</span> 
					</div>
					</td>
					<td><?php echo $data['sep_group']; ?></td>
					<td><?php echo sepopups_cls_dbquery::sepopups_common_text($data['sep_status']); ?></td>
					<?php
					$sep_start = $data['sep_start'];
					$sep_end = $data['sep_end'];
					$now_strtotime = strtotime(date("Y-m-d"));
					$str_strtotime = strtotime($data['sep_start']);
					$end_strtotime = strtotime($data['sep_end']);
					if($end_strtotime < $now_strtotime) {
						$sep_end = '<span style="color:#FF0000;">' . $data['sep_end'] . '</span>';
					}
					if($str_strtotime > $now_strtotime) {
						$sep_start = '<span style="color:#FF0000;">' . $data['sep_start'] . '</span>';
					}
					?>
					<td><?php echo $sep_start; ?></td>
					<td><?php echo $sep_end; ?></td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else {
			?><tr><td colspan="6" align="center"><?php _e('No records available', 'simple-exit-popup'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('sep_form_show'); ?>
		<input type="hidden" name="frm_sep_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo SEPOPUP_ADMIN_URL; ?>&amp;ac=add">
	  <input class="button button-primary" type="button" value="<?php _e('Add New', 'simple-exit-popup'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2020/06/20/simple-exit-popup-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Short Code', 'simple-exit-popup'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2020/06/20/simple-exit-popup-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Help', 'simple-exit-popup'); ?>" /></a>
	  </div>
	</div>
</div>