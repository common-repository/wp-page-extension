<?php
/*
Plugin Name:	WP Page Extension
Description: 	This plugin will allow you to add extensions to the end of your pages. Choices are:  .html, .htm, .php, .xml, .asp and .shtml.
Author: 		Daddy Design
Version:		1.1
Author URI:		http://www.daddydesign.com/
*/

class wp_fake_url{
	//	Define constructor
	function wp_fake_url(){			
		add_action('admin_init',array(&$this,'fake_url_init'),-1);
		add_action('template_redirect',array(&$this,'check_url'));
		//	add_action('admin_menu',array(&$this,'admin_menu'));	
		add_action('add_meta_boxes',array(&$this,'fake_page_box'));
		add_action('wp_insert_post',array(&$this,'save_fake_url'),1,2);
		add_filter('user_trailingslashit',array(&$this,'no_page_slash'),66,2);
	}	
	
	//	End of defining constructor	
	function fake_url_init(){
		global $wpdb,$wp_rewrite;						
		
		//	echo '<pre>'; print_r($fake_status_detail); exit;				
		$fake_status	=	get_option("current_extension");
		
		if($fake_status==0){
			if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
				$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				$wp_rewrite->flush_rules();			
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
				$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				$wp_rewrite->flush_rules();
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
				$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '/';
				$wp_rewrite->flush_rules();
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
				$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure); exit;
				$wp_rewrite->flush_rules();
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
				$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				$wp_rewrite->flush_rules();
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
				$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				$wp_rewrite->flush_rules();
			}
			
			if($fake_status==0){
				$wp_rewrite->page_structure		=	$wp_rewrite->page_structure;				
				$wp_rewrite->flush_rules();
			}			
		}else if($fake_status==1){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.html')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
				$wp_rewrite->flush_rules();
			}
		}else if($fake_status==2){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.htm')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.htm';
				$wp_rewrite->flush_rules();				
			}	
		}else if($fake_status==3){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.php')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.php';
				$wp_rewrite->flush_rules();
			}		
		}else if($fake_status==4){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.xml')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.xml';
				$wp_rewrite->flush_rules();
			}
		}else if($fake_status==5){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.asp')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.asp';
				$wp_rewrite->flush_rules();
			}
		}else if($fake_status==6){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.shtml';
				$wp_rewrite->flush_rules();
			}
		}			
	}
	
	function no_page_slash($string, $type){
		global $wp_rewrite;						
		$ext_enabled	=	get_option('extension_enabled');
		$ext_curr		=	get_option('current_extension');
		
		
		if(is_admin()){
			if($ext_curr!=0){				
				if($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
					return untrailingslashit($string);
				}else{
					return $string;
				}			
			}else{
				if($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
					return trailingslashit($string);
				}else{
					return $string;
				}
			}	
		}else{
			if($ext_enabled!=0){				
				if($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
					return untrailingslashit($string);
				}else{
					return $string;
				}			
			}else{
				if($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page'){
					return trailingslashit($string);
				}else{
					return $string;
				}
			}		
		}
		
	}
	
	//	Add plugin menu in settings
	function admin_menu(){
		add_menu_page('Generate Post', 'Generate Post', 'manage_options','plugin-admin-view', array(&$this, 'plugin_view'));
		add_options_page('WP FAKE URL','WP FAKE URL', 'manage_options','wp-fake-url', array(&$this, 'plugin_view'));
	}
	
	//	End of add plugin menu in settings
	
	//	Add meta box in page
	function fake_page_box(){
		add_meta_box('fake_page_box_sectionid', __( 'Choose Page Extension', 'fake_page_box_textdomain' ),array( &$this, 'fake_page_box_body' ),'page', 'normal' );
	}
	
	//	End of adding meta box in page
	
	//	Add content for page box
	function fake_page_box_body($post){
		global	$wpdb;		
		$page_id			=	$post->ID;
		$fake_url_detail	=	$wpdb->get_results("SELECT * FROM page_fake_url WHERE page_id='".$page_id."'");
		
		$default			=	'';
		$html				=	'';
		$htm				=	'';
		$php				=	'';
		$xml				=	'';
		$asp				=	'';
		
		if(!empty($fake_url_detail)){
			//	echo '<pre>'; print_r($fake_url_detail); exit;
			$url_code		=	$fake_url_detail[0]->fake_url_code;			
		}else{
			$url_code		=	0;
		}
		
		if($url_code==0){
			$default		=	'checked';
		}else if($url_code==1){
			$html			=	'checked';
		}else if($url_code==2){
			$htm			=	'checked';
		}else if($url_code==3){
			$php			=	'checked';
		}else if($url_code==4){
			$xml			=	'checked';
		}else if($url_code==5){
			$asp			=	'checked';
		}else if($url_code==6){
			$shtml			=	'checked';
		}	
		
		echo "<br/>";
		echo '<table>';
		echo '<tr>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension0" value="0" '.$default.'/> Default(none)</td>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension1" value="1" '.$html.'/> Html(.html)</td>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension2" value="2" '.$htm.'/> Htm(.htm)</td>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension3" value="3" '.$php.'/> Php(.php)</td>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension4" value="4" '.$xml.'/> Xml(.xml)</td>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension5" value="5" '.$asp.'/> Asp(.asp)</td>';
		echo '<td>&nbsp;&nbsp;<input type="radio" style="vertical-align:baseline !important" name="fake_extension" id="fake_extension6" value="6" '.$shtml.'/> Shtml(.shtml)</td>';
		echo '</tr>';		
		echo '</table>';	
	}
	
	//	End of adding content for page box		
	
	//	Create table
	function creates_tables(){
		global $wpdb,$wp_rewrite;				
		$fake_url_table			=	'page_fake_url';
		$fake_status			=	'fake_status';
		
		if(!strpos($wp_rewrite->get_page_permastruct(), '.asp')){
			$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.asp';
		}
		
		$wp_rewrite->flush_rules();
		
		if($wpdb->get_var("SHOW TABLES LIKE ' $fake_url_table'")!=  $fake_url_table) {
			$sql = "CREATE TABLE " .  $fake_url_table . " (
						`fake_url_id` int(11) NOT NULL AUTO_INCREMENT,
						`page_id`	int(11) NOT NULL,
						`fake_url_code`	int(11) NOT NULL,
						PRIMARY KEY (`fake_url_id`)
					);";				
					
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}	
		
		add_option('extension_enabled','0');
		add_option('current_extension','0');
		add_option('extFlag','0');
	}
	
	//	End of creating table
	
	//	Make fake url	
	function check_url($post){
		global $wpdb,$post,$wp_rewrite;	
		//	print_r($post);
		
		$page_id			=	$post->ID;
		
		if(empty($post)){
			//	echo 'Page is empty'; exit;	
			$pagePath		=	$_SERVER["REQUEST_URI"];				
			$pathArray		=	explode('/',$pagePath);		
			
			$pageExt		=	trim(end($pathArray));

			if($pageExt==""){
				 array_pop($pathArray);
				 $pageExt		=	trim(end($pathArray));		
			}					
			
			$extArray		=	explode('.',$pageExt);
			
			if(count($extArray)>0){
				$pageExt		=	$extArray[0];
				$pageDetail		=	$wpdb->get_results("SELECT * FROM wp_posts where post_name='".$pageExt."'");				
				$page_id		= 	$pageDetail[0]->ID;
				$extValue		=	$extArray[1];
				
				if($extValue=='html'){
					update_option('extFlag','1');
				}else if($extValue=='htm'){
					update_option('extFlag','2');
				}else if($extValue=='php'){
					update_option('extFlag','3');
				}else if($extValue=='xml'){
					update_option('extFlag','4');
				}else if($extValue=='asp'){
					update_option('extFlag','5');
				}else if($extValue=='shtml'){
					update_option('extFlag','6');
				}
				
			}else{			
				$pageDetail		=	$wpdb->get_results("SELECT * FROM wp_posts where post_name='".$pageExt."'");				
				$page_id		= 	$pageDetail[0]->ID;
			}			
		}	
		
		$pagePath			=	$_SERVER["REQUEST_URI"];				
		$pathArray			=	explode('.',$pagePath);				
		$extension			=	end($pathArray);
		$fake_url_detail	=	$wpdb->get_results("SELECT * FROM page_fake_url WHERE page_id='".$page_id."'");		
		$ext_enabled		=	get_option('extension_enabled');
		
		if(!empty($fake_url_detail)){
			$code		=	$fake_url_detail[0]->fake_url_code;
		}else{
			$code		=	0;			
		}
		
		if($code==1){
			if($extension!='html'){					
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}				
					
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}
					
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
							
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
				$wp_rewrite->flush_rules();			
				$pageLink	=	get_permalink($page_id); 
				
				update_option('extension_enabled','1');
				header('Location:'.$pageLink);
				exit;					
			}else if(get_option('extFlag')!=0){
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				update_option('extFlag','0');
				header('Location:'.$pageLink);
				exit;				
			}			
		}else if($code==2){				
			if($extension!='htm'){					
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.htm';
				$wp_rewrite->flush_rules();			
				$pageLink	=	get_permalink($page_id);
				
				update_option('extension_enabled','1');	
				header('Location:'.$pageLink);
				exit;					
			}else if(get_option('extFlag')!=0){
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.htm';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				update_option('extFlag','0');
				header('Location:'.$pageLink);
				exit;				
			}
		}else if($code==3){
			if($extension!='php'){					
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.php';
				$wp_rewrite->flush_rules();			
				$pageLink	=	get_permalink($page_id); 
				
				update_option('extension_enabled','1');
				header('Location:'.$pageLink);
				exit;					
			}else if(get_option('extFlag')!=0){
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.php';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				update_option('extFlag','0');
				header('Location:'.$pageLink);
				exit;				
			}
		}else if($code==4){
			if($extension!='xml'){					
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.xml';
				$wp_rewrite->flush_rules();			
				update_option('extension_enabled','1');
				$pageLink	=	get_permalink($page_id); 
				header('Location:'.$pageLink);
				exit;					
			}else if(get_option('extFlag')!=0){
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.xml';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				update_option('extFlag','0');
				header('Location:'.$pageLink);
				exit;				
			}					
		}else if($code==5){		
			if($extension!='asp'){	
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.asp';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				header('Location:'.$pageLink);
				exit;														
			}else if(get_option('extFlag')!=0){
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
					$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.asp';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				update_option('extFlag','0');
				header('Location:'.$pageLink);
				exit;				
			}			
		}else if($code==6){
			if($extension!='shtml'){					
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.shtml';
				$wp_rewrite->flush_rules();			
				$pageLink	=	get_permalink($page_id); 
				
				update_option('extension_enabled','1');
				header('Location:'.$pageLink);
				exit;					
			}else if(get_option('extFlag')!=0){
				if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
					$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
				}				
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
					$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
					$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
					$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
				}
				
				if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
					$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
				}
				
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.shtml';				
				$wp_rewrite->flush_rules();								
				$pageLink	=	get_permalink($page_id);				
				update_option('extension_enabled','1');				
				update_option('extFlag','0');
				header('Location:'.$pageLink);
				exit;				
			}	
		}else if($ext_enabled=='1'){
			$currentURl		=	$this->curPageURL();
			$urlArray		=	explode("/",$currentURl);
			$extension		=	end($urlArray);
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
				$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
			}				
				
			if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
				$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
				$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
				$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
				$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
			}
			
			if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
				$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
			}
			
			$wp_rewrite->page_structure = $wp_rewrite->page_structure . '/';
			$wp_rewrite->flush_rules();
			update_option('extension_enabled','0');
			$permanlink		=	get_permalink($page_id); 
			header("Location:".$permanlink);
			exit;			
		}				
	}
	
	//	End of making fake url
	
	//	Save fake url
	function save_fake_url($post_id,$post){
		global $wpdb,$wp_rewrite;		
		extract($_POST);		
		$parent			=	$post->post_parent;		
		$fake_status	=	5;
		
		if($fake_status==5){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.asp')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.asp';
			}
		}
		
		if(!empty($parent)){
			$page_id		=	$parent;
		}else{
			$page_id		=	$post->ID;
		}		
		
		$fake_url_detail	=	$wpdb->get_results("SELECT * FROM page_fake_url WHERE page_id='".$page_id."'");
		
		//	echo 'INSERT INTO page_fake_url (page_id,fake_url_code) VALUES ("'.$page_id.'","'.$_POST['fake_extension'].'")'; exit;
		
		if(!empty($fake_url_detail)){					
			$update_record		=	$wpdb->query("UPDATE page_fake_url SET fake_url_code='".$_POST['fake_extension']."' WHERE page_id='".$page_id."'");			
			$fake_status_detail	=	$wpdb->get_results("SELECT * FROM fake_status");				
		}else{			
			$record_inserted	=	$wpdb->query('INSERT INTO page_fake_url (page_id,fake_url_code) VALUES ("'.$page_id.'","'.$_POST['fake_extension'].'")');
			$fake_status_detail	=	$wpdb->get_results("SELECT * FROM fake_status");					
		}		
		
	
		
		$fake_status			=	$_POST['fake_extension'];
		update_option('current_extension',$fake_status);
		
		if($fake_status==1){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.html')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
				$wp_rewrite->flush_rules();				
			}			
		}else if($fake_status==2){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.htm')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.htm';
				$wp_rewrite->flush_rules();
			}			
		}else if($fake_status==3){			
			if(!strpos($wp_rewrite->get_page_permastruct(), '.php')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.php';
				$wp_rewrite->flush_rules();
			}					
		}else if($fake_status==4){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.xml')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.xml';
				$wp_rewrite->flush_rules();
			}			
		}else if($fake_status==5){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.asp')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.asp';
				$wp_rewrite->flush_rules();
			}			
		}else if($fake_status==6){
			if(!strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
				$wp_rewrite->page_structure = $wp_rewrite->page_structure . '.shtml';
				$wp_rewrite->flush_rules();
			}			
		}				
	}
	 
	//	End of saving fake url	
	
	function deactivate(){		
		global $wp_rewrite,$wpdb;
		
		if(strpos($wp_rewrite->get_page_permastruct(), '.html')){
			$wpdb->query("UPDATE fake_status SET fake_status ='0'");
			$wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
			$wp_rewrite->flush_rules();			
		}
		
		if(strpos($wp_rewrite->get_page_permastruct(), '.htm')){
			$wpdb->query("UPDATE fake_status SET fake_status ='0'");
			$wp_rewrite->page_structure = str_replace(".htm","",$wp_rewrite->page_structure);
			$wp_rewrite->flush_rules();
		}
		
		if(strpos($wp_rewrite->get_page_permastruct(), '.php')){
			$wpdb->query("UPDATE fake_status SET fake_status ='0'");
			$wp_rewrite->page_structure = str_replace(".php","",$wp_rewrite->page_structure);
			$wp_rewrite->page_structure = $wp_rewrite->page_structure . '/';
			$wp_rewrite->flush_rules();
		}
		
		if(strpos($wp_rewrite->get_page_permastruct(), '.xml')){
			$wpdb->query("UPDATE fake_status SET fake_status ='0'");
			$wp_rewrite->page_structure = str_replace(".xml","",$wp_rewrite->page_structure);
			$wp_rewrite->flush_rules();
		}
			
		if(strpos($wp_rewrite->get_page_permastruct(), '.asp')){
			$wpdb->query("UPDATE fake_status SET fake_status ='0'");
			$wp_rewrite->page_structure = str_replace(".asp","",$wp_rewrite->page_structure);
			$wp_rewrite->flush_rules();
		}
		
		if(strpos($wp_rewrite->get_page_permastruct(), '.shtml')){
			$wpdb->query("UPDATE fake_status SET fake_status ='0'");
			$wp_rewrite->page_structure = str_replace(".shtml","",$wp_rewrite->page_structure);
			$wp_rewrite->flush_rules();
		}	
		
		delete_option('extension_enabled');
		delete_option('current_extension');
		delete_option('extFlag');
	}
	
	function curPageURL() {
		$pageURL = 'http';
		
		if($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		
		$pageURL .= "://";
		
		if($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	
		return $pageURL;
	}

}

//	Make object of class	
$ob_wp_fake_url	=	new wp_fake_url();	

//	End of make object of class

if(isset($ob_wp_fake_url)){
	register_activation_hook( __FILE__, array(&$ob_wp_fake_url,'creates_tables') );
	register_deactivation_hook( __FILE__, array(&$ob_wp_fake_url,'deactivate') );
}

?>