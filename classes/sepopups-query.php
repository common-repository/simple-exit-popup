<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class sepopups_cls_dbquery {

	public static function sepopups_count($id = 0) {

		global $wpdb;
		$result = '0';
		if(!is_numeric($id)) { 
			return $result;
		}
		
		if($id <> "" && $id > 0) {
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "sepopups WHERE sep_id = %d", array($id));
		} 
		else {
			$sSql = "SELECT COUNT(*) AS count FROM " . $wpdb->prefix . "sepopups";
		}
		
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function sepopups_select_bygroup($group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "sepopups";

		if($group <> "") {
			$sSql = $sSql . " WHERE sep_group = %s";
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
		}
		
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function sepopups_select() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "sepopups";
		$sSql = $sSql . " Order by sep_id desc, sep_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function sepopups_select_byid($id = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "sepopups";

		if($id <> "") {
			$sSql = $sSql . " WHERE sep_id = %d LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($id));
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		
		return $arrRes;
	}
	
	public static function sepopups_select_shortcode($id = "", $group = "") {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT * FROM " . $wpdb->prefix . "sepopups WHERE sep_status = 'Yes'";
		$sSql .= " AND ( sep_start <= NOW() or sep_start = '0000-00-00' )";
		$sSql .= " AND ( sep_end >= NOW() or sep_end = '0000-00-00' )";
		
		if($id <> "" && $id <> "0") {
			$sSql .= " AND sep_id = %d LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($id));
		}
		elseif($group <> "") {
			$sSql .= " AND sep_group = %s Order by rand() LIMIT 0,1";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else {
			$sSql = $sSql . " Order by rand() LIMIT 0,1";
		}
		
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		
		return $arrRes;
	}
	
	public static function sepopups_group() {

		global $wpdb;
		$arrRes = array();
		$sSql = "SELECT distinct(sep_group) FROM " . $wpdb->prefix . "sepopups order by sep_group";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}

	public static function sepopups_delete($id = "") {

		global $wpdb;

		if($id <> "") {
			$sSql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "sepopups WHERE sep_id = %d LIMIT 1", $id);
			$wpdb->query($sSql);
		}

		return true;
	}

	public static function sepopups_insert($data = array()) {

		global $wpdb;
		
		$sql = $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "sepopups (
			sep_title, 
			sep_text,
			sep_group,
			sep_style1,
			sep_style2,
			sep_style3,
			sep_status,
			sep_start,
			sep_end
			) 
			VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)", 
			array(
			$data["sep_title"], 
			$data["sep_text"], 
			$data["sep_group"], 
			$data["sep_style1"], 
			$data["sep_style2"], 
			$data["sep_style3"], 
			$data["sep_status"],
			$data["sep_start"],
			$data["sep_end"]
			));

		$wpdb->query($sql);
		
		return "inserted";
	}
	
	public static function sepopups_update($data = array()) {

		global $wpdb;
		
		$sSql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "sepopups SET 
			sep_title = %s, 
			sep_text = %s, 
			sep_group = %s, 
			sep_style1 = %s, 
			sep_style2 = %s, 
			sep_style3 = %s, 
			sep_status = %s, 
			sep_start = %s, 
			sep_end = %s 
			WHERE sep_id = %d LIMIT 1", 
			array(
			$data["sep_title"], 
			$data["sep_text"], 
			$data["sep_group"], 
			$data["sep_style1"], 
			$data["sep_style2"], 
			$data["sep_style3"], 
			$data["sep_status"], 
			$data["sep_start"], 
			$data["sep_end"], 
			$data["sep_id"]
			));

		$wpdb->query($sSql);
		return "update";
	}

	public static function sepopups_default() {

		$count = sepopups_cls_dbquery::sepopups_count($id = 0);
		if($count == 0){
			
			$today = date("Y-m-d");
			$text1 = '<p><strong>Hold On For Just One Second!</strong></p>';
			$text1 .= '<p>This popup window display in the browser when users about to leave the page. When the user moves his mouse outside the browser window it appear in the screen.</p>'; 
			$text1 .= '<a class="closebutton" href="http://www.gopiplus.com/" onClick="simpleexitpopup.hidepopup()">Open Demo</a>';	
			
			$data['sep_title'] = 'This is sample exit popup title.';
			$data['sep_text'] = $text1;
			$data['sep_group'] = 'General';
			$data['sep_style1'] = 'max-width: 700px;border: 10px solid black;background: white;padding: 5px;';
			$data['sep_style2'] = 'entry-content';
			$data['sep_style3'] = '';
			$data['sep_style4'] = '';
			$data['sep_status'] = 'Yes';
			$data['sep_start'] = $today;
			$data['sep_end'] = '9999-12-31';
			sepopups_cls_dbquery::sepopups_insert($data);
		}
	}
	
	public static function sepopups_common_text($value) {
		
		$returnstring = "";
		switch ($value) {
			case "Yes":
				$returnstring = '<span style="color:#006600;">Yes</span>';
				break;
			case "No":
				$returnstring = '<span style="color:#FF0000;">No</span>';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
}

?>