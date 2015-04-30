<?php
/* Table name form */
function mk_list_form() {
	global $table_prefix, $wpdb;
	$wp_my_new_table_name = $table_prefix . "mk_form_name";
	$wp_my_new_table_content = $table_prefix . "mk_form_content";
	//$myrows= $wpdb->get_results("SELECT * FROM `".$wp_my_new_table."`");
	$myrows= $wpdb->get_results('SELECT id,title,val FROM `'.$wp_my_new_table_name.'`,`'.$wp_my_new_table_content.'` WHERE id_form=id AND name="list_name"');
	$tmp = array();
	$i=0;
	foreach($myrows AS $rows){
		$tmp[$i] = array();
		$tmp[$i]['id'] = $rows->id;
		$tmp[$i]['title'] = $rows->title;
		$tmp[$i]['val'] = ($rows->val!=NULL)?$rows->val:0;
		$i++;
	}
	return $tmp;
}
function mk_save_form($tab) {
	global $table_prefix, $wpdb;
	$wp_my_new_table = $table_prefix . "mk_form_name";
	
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO `".$wp_my_new_table."` (`title`)
			VALUES (%s)",
			$tab['new_form']
		)
	);
	
	$myrows= $wpdb->get_results("SELECT LAST_INSERT_ID() id FROM " .$wp_my_new_table);
	$tmp = array();
	foreach($myrows AS $rows){
		$tmp = $rows->id;
	}
	return $tmp;
}

function mk_delete_form($id) {
	global $table_prefix, $wpdb;
	$mk_form_name = $table_prefix . "mk_form_name";
	$mk_form_content = $table_prefix . "mk_form_content";
	
	$name= $wpdb->get_results("SELECT title FROM `".$mk_form_name."` WHERE id=".$id);
	
	$sql1 = "DELETE FROM `".$mk_form_name."` WHERE id=".$id;
    $wpdb->query($sql1);
	
	$sql2 = "DELETE FROM `".$mk_form_content."` WHERE id_form=".$id;
    $wpdb->query($sql2);
	
	return $name;
}


/* Table form content */
function mk_read_form($id) {
	global $table_prefix, $wpdb;
	$wp_my_new_table = $table_prefix . "mk_form_content";
	$myrows= $wpdb->get_results("SELECT * FROM `".$wp_my_new_table."` WHERE id_form = ".$id);
	$tmp = array();
	foreach($myrows AS $rows){
		$tmp[$rows->name] = $rows->val;
	}
	return $tmp;
}
function mk_save_content_form($tab) {

	global $table_prefix, $wpdb;
	$wp_my_new_table = $table_prefix . "mk_form_content";

	foreach($tab["form"] AS $data){
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO `".$wp_my_new_table."` (`name`,`val`,`id_form`) 
				VALUES (%s,%s,%d)
				ON DUPLICATE KEY UPDATE val = VALUES( val )",
				$data['name'],
				$data['val'],
				$tab['id']
			)
		);
	}
}
/* Plugin form */
class mk_plugin_form extends WP_widget {

	function mk_plugin_form() {
		$options = array (
			"classname" 	=> "mk-form",
			"description" 	=>  __("description_plugin_barchoix","mailkitchen")
		);
		$this->WP_widget("mk-form-plugin",__("titre_plugin_barchoix","mailkitchen"),$options);
	}
	
	function widget($args,$instance) {
		extract($args);
		$rows 	= mk_read_form($instance["form_name"]);
		$ident  = (uniqid(rand(), TRUE));
		if(!empty($rows)){
			echo $before_widget;
			echo $before_title.$rows["titre"].$after_title;
			?>
			<p><?php echo $rows["presentation"];?></p>
			<div class="mk-form" data-id="<?php echo $ident;?>">
				<label><?php echo $rows["lab-email"];?></label><br/>
				<input type="hidden" class="mk_num_form"  data-id="<?php echo $ident;?>" value="<?php echo $instance["form_name"];?>" />
				<input type="text" class="mk_insert_mail"  data-id="<?php echo $ident;?>" name="mk_insert_mail" placeholder="<?php echo $rows["chp-email"];?>"><br/><br/>
				<input class="mk_form_validate"  data-id="<?php echo $ident;?>" type="button" value="<?php echo $rows["btn"];?>"/>
				<p style="font-size=0.7em;"><?php echo $rows["mention"];?></p>
			</div>
			<p class="mk-form-valide"  data-id="<?php echo $ident;?>" style="display:none;background-color:green;color: #FFFFFF;font-weight:bold;text-align:center;" ><?php echo $rows["msg-valide"];?></p>
			<p class="mk-form-erreur"  data-id="<?php echo $ident;?>" style="display:none;background-color:red;color: #FFFFFF;font-weight:bold;text-align:center;" ><?php echo $rows["msg-erreur"];?></p>
			
			<div class="mk-loader-form" data-id="<?php echo $ident;?>"></div>
			<?php
			echo $after_widget;
		}
	}
	
	function form() {
		$listform = mk_list_form();
		if(!empty($listform)) {
	?>
			<p>
			<select name="<?php echo $this->get_field_name("form_name"); ?>">
					<option><?php echo __("titre_plugin_edition","mailkitchen"); ?></option>
					<?php
					$listform = mk_list_form();
					foreach($listform as $liste) {
						echo "<option data-list='".$liste['val']."'value='".$liste['id']."'>".$liste['title']."</option>";
					}
					?>
			</select>
			</p/>
			<p class="noList"><?php echo __("abs_list_plugin_edition","mailkitchen"); ?></p>
			<p><?php echo __("lien_plugin_edition","mailkitchen"); ?></p>
	<?php
		}
		else{
	?>
		<p><?php echo __("description_plugin_edition","mailkitchen"); ?><a href="admin.php?page=create-your-form"><?php echo __("lien_plugin_edition","mailkitchen"); ?></a></p>
	<?php
		}
	}
	
	function update($new, $old) {
		return $new;
	}
	
	function save_form()
	{
		if (isset($_POST['mk_insert_mail']) && !empty($_POST['mk_insert_mail'])) {
			$tab = mk_connexion();
			$service=$tab['service'];
			$token=$tab['token'];
			$liste=$service->ImportMember(array(),array($_POST['mk_insert_mail']),$token);
			
		}
	}
}



?>