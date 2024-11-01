<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class sepopups_cls_registerhook {
	public static function sepopups_activation() {
	
		global $wpdb;

		add_option('simple-exit-popup', "1.0");

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();
	
		$sepopups_default_tables = "CREATE TABLE {$wpdb->prefix}sepopups (
										sep_id INT unsigned NOT NULL AUTO_INCREMENT,
										sep_title VARCHAR(1024) NOT NULL default '',
										sep_text TEXT,
										sep_group VARCHAR(20) NOT NULL default 'General',
										sep_style1 VARCHAR(1024) NOT NULL default 'border:1px solid black',
										sep_style2 VARCHAR(1024) NOT NULL default 'border:1px solid black',
										sep_style3 VARCHAR(1024) NOT NULL default 'border:1px solid black',
										sep_style4 VARCHAR(1024) NOT NULL default 'border:1px solid black',
										sep_status VARCHAR(3) NOT NULL default 'Yes',
										sep_start date NOT NULL DEFAULT '0000-00-00', 
										sep_end date NOT NULL DEFAULT '9999-12-31',
										sep_fxclass VARCHAR(10) NOT NULL default 'random',
										sep_displayfreq VARCHAR(10) NOT NULL default 'always',
										sep_delayregister INT unsigned NOT NULL default 3000,
										sep_hideaftershow VARCHAR(10) NOT NULL default 'false',
										sep_mobileshowafter INT unsigned NOT NULL default 3000,
										sep_delayshow INT unsigned NOT NULL default 200,
										PRIMARY KEY (sep_id)
									) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sepopups_default_tables );
		
		$sepopups_default_tablesname = array( 'sepopups' );
	
		$sepopups_has_errors = false;
		$sepopups_missing_tables = array();
		foreach($sepopups_default_tablesname as $table_name) {
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $wpdb->prefix.$table_name . "'")) != strtoupper($wpdb->prefix.$table_name)) {
				$sepopups_missing_tables[] = $wpdb->prefix.$table_name;
			}
		}

		if($sepopups_missing_tables) {
			$errors[] = __( 'These tables could not be created on installation ' . implode(', ',$sepopups_missing_tables), 'simple-exit-popup' );
			$sepopups_has_errors = true;
		}
		
		if($sepopups_has_errors) {
			wp_die( __( $errors[0] , 'simple-exit-popup' ) );
			return false;
		} 
		else {
			sepopups_cls_dbquery::sepopups_default();
		}
		
		return true;
	}

	public static function sepopups_deactivation() {
		// do not generate any output here
	}

	public static function sepopups_adminoptions() {
	
		global $wpdb;
		$current_page = isset($_GET['ac']) ? sanitize_text_field($_GET['ac']) : '';
		
		switch($current_page) {
			case 'edit':
				require_once(SEPOPUP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-edit.php');
				break;
			case 'add':
				require_once(SEPOPUP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-add.php');
				break;
			default:
				require_once(SEPOPUP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-show.php');
				break;
		}
	}
	
	public static function sepopups_addtomenu() {
	
		if (is_admin()) {
			add_options_page( __('Simple exit popup', 'simple-exit-popup'), 
								__('Simple exit popup', 'simple-exit-popup'), 'manage_options', 
									'simple-exit-popup', array( 'sepopups_cls_registerhook', 'sepopups_adminoptions' ) );
		}
	}
	
	public static function sepopups_adminscripts() {
	
		if(!empty($_GET['page'])) {
			switch (sanitize_text_field($_GET['page'])) {
				case 'simple-exit-popup':
					wp_register_script( 'sepopups-adminscripts', plugin_dir_url( __DIR__ ) . '/pages/setting.js', '', '', true );
					wp_enqueue_script( 'sepopups-adminscripts' );
					$sepopups_select_params = array(
						'sep_title'  		=> __( 'Please enter popup box title.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_text'  		=> __( 'Please enter popup content.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_group'  		=> __( 'Please select group for this popup box.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_status'  		=> __( 'Please select display status of this popup box.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_start'  		=> __( 'Please enter display start date of this popup box, format YYYY-MM-DD.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_end'  			=> __( 'Please enter display end date of this popup box, format YYYY-MM-DD.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_style1'  		=> __( 'Please enter css inline style info for popup window.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_style2'  		=> __( 'Please enter css class info for popup window.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_style3'  		=> __( 'Please enter css inline style info for close buttom.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_numletters'  	=> __( 'Please input numeric and letters only.', 'sepopups-select', 'simple-exit-popup' ),
						'sep_delete'  		=> __( 'Do you want to delete this record?', 'sepopups-select', 'simple-exit-popup' ),
					);
					wp_localize_script( 'sepopups-adminscripts', 'sepopups_adminscripts', $sepopups_select_params );
					break;
			}
		}
	}
	
	public static function sepopups_popupstyle() {
		echo '<style>';
		echo '#sep_wrapper {display: flex;position: fixed;left: 0;top: 0;width: 100%;height: 100%;z-index: 1000;pointer-events: none;align-items: center;justify-content: center;}';
		echo '#sep_wrapper .veil{left: 0;top: 0;width: 100%;height: 100%;position: fixed;background-color: rgba(0,0,0,.7);content: "";z-index: 1;display: none;cursor: default;}';
		echo '#sep_wrapper.open{pointer-events: auto;}';
		echo '#sep_wrapper.open .veil{display: block;}';
		echo '#sep_wrapper.open div.simpleexitpopupclose{display: block;}';
		echo 'div.simpleexitpopupclose{ width: 70px;height: 70px;overflow: hidden;display: none;position: fixed;cursor: pointer;text-indent: -1000px;z-index: 3;top: 10px;right: 10px;}';
		echo '#sep_wrapper.open .simpleexitpopup_c1{ visibility: visible;}';
		echo '.simpleexitpopup_c1 { ';
			echo 'width: 90%;';
			echo 'max-width: 700px;';
			echo 'z-index: 2;';
			echo '-webkit-box-sizing: border-box;';
			echo '-moz-box-sizing: border-box;';
			echo 'box-sizing: border-box;';
			echo 'position: relative;';
			echo 'left: 0;';
			echo 'top: -100px; ';
			echo '-webkit-animation-duration: .5s; ';
			echo 'animation-duration: .5s;'; 
			echo 'visibility: hidden;';
		echo '}';
		
		echo '.simpleexitpopup_c1 .closebutton{ ';
			echo 'display: inline-block;';
			echo 'text-decoration: none;';
			echo 'border-radius: 5px;';
			echo 'padding: 15px;';
			echo 'background: #15C5FF;';
			echo 'display: block;';
			echo 'width: 80%;';
			echo 'font: bold 24px Arial;';
			echo 'box-shadow: 0 0 15px gray, 0 0 10px gray inset;';
			echo 'margin: 10px auto;';
			echo 'text-align: center;';
			echo 'color: white !important;';
		echo '}';
		echo '@media screen and ( max-height: 765px ){ .simpleexitpopup_c1{ top: 0; }}';
		echo '</style>';
	}
}

function sepopups_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'animate.min', SEPOPUP_URL . 'inc/animate.min.css');
		//wp_enqueue_style( 'simple-exit-popup', SEPOPUP_URL . 'inc/simple-exit-popup.css'); Not required. added in the head.
		wp_enqueue_script( 'simple-exit-popup', SEPOPUP_URL . 'inc/simple-exit-popup.js');
	}
}

class sepopups_cls_shortcode {
	public function __construct() {
	}
	
	public static function sepopups_shortcode( $atts ) {
		ob_start();
		if (!is_array($atts)) {
			return '';
		}
		
		//[simple-exit-popup group="General"]
		//[simple-exit-popup id="1"]
		$atts = shortcode_atts( array(
				'group'	=> '',
				'id'	=> ''
			), $atts, 'simple-exit-popup' );

		$group 	= isset($atts['group']) ? sanitize_text_field($atts['group']) : '';
		$id 	= isset($atts['id']) ? intval($atts['id']) : '';

		$data = array(
			'group' => $group,
			'id' 	=> $id
		);
		
		self::sepopups_render( $data );

		return ob_get_clean();
	}
	
	public static function sepopups_render( $data = array() ) {	
		
		$sep = "";
		
		if(count($data) == 0) {
			return $ifbox;
		}

		$id = intval($data['id']);
		$group 	= sanitize_text_field($data['group']);

		$popup = '$popup';
		$data = sepopups_cls_dbquery::sepopups_select_shortcode($id, $group);
		if(count($data) > 0 ) {
			$sep_text = $data['sep_text'];
			$sep_style1 = $data['sep_style1'];
			$sep_style2 = $data['sep_style2']; // Class name
			$sep_style3 = $data['sep_style3'];
			
			if($sep_text <> "" && strlen($sep_text) > 0) {
				$txt =  trim($sep_text);
				$txt = do_shortcode($txt);
				$txt = str_replace("\n", "<br />", $txt);
			}
			
			$ifbox = '<style>';
			$ifbox .= '.simpleexitpopup_c1 {';
			$ifbox .= $sep_style1;
			$ifbox .= '}';
			$ifbox .= '</style>';
			
			$ifbox .= '<script>';
			$ifbox .= 'jQuery(function(){';
				$ifbox .= 'simpleexitpopup.init({';
					$ifbox .= "contentsource: ['id', 'simpleexitpopup'],";
					$ifbox .= "fxclass: 'random',";
					//displayfreq
					//"always": Pop up is shown each time the page is loaded and the user tries to exit.
					//"session": Pop up is shown once per browser session site wide.
					//"Xhour or Xday": Pop up is shown once every x hours or days. Replace "x" with a number, such as "5hour" or "2day".
					$ifbox .= "displayfreq: 'always',";
					//delayregister: Sets the delay in milliseconds after the page loads before the exit pop is registered and reacts 
					//to mouse movements. A value of 2000 for example means if the user tries to exit the page (via mouse movement) 
					//within 2 seconds after the page loads, the exit pop wont be shown.
					$ifbox .= "delayregister: 1000,";
					//hideaftershow: Boolean on whether the exit pop should remain hidden once its been closed, 
					//so subsequent mouse movement to exit the page will no longer trigger the exit pop
					$ifbox .= "hideaftershow: true,";
					$ifbox .= "mobileshowafter: 3000,";
					//This option helps eliminate "jumpy" exit pops that pops up as soon as the user moves his mouse outside 
					//the browsers top boundary. The default value of 200 (for 200 milliseconds) means if the user quickly moves 
					//the mouse outside the boundary and back into the page within 200 milliseconds, the pop up is not triggered
					//$ifbox .= "delayshow: 500,";
					$ifbox .= "onddexitpop: function($popup){ console.log('Exit Class Name: ' + simpleexitpopup.settings.fxclass) }";
				$ifbox .= '})';
			$ifbox .= '})';
			$ifbox .= '</script>';
			
			$ifbox .= '<div id="simpleexitpopup" class="simpleexitpopup_c1 '.$sep_style2.'">';
			$ifbox .= '<p>';
			$ifbox .= stripslashes($txt);
			$ifbox .= '</p>';
			$ifbox .= '</div>';
			
		}
		echo $ifbox;
	}
	
}
?>