<?php

function io_form_select($values, $selected = array(), $notto = "", $user = false) {
	foreach($values AS $key_1 => $value_1) {
		?><optgroup label="&nbsp;&nbsp;<?php echo $key_1; ?>">
<?php	foreach($value_1 AS $key_2 => $value_2) {
			if(!is_array($value_2) && $value_2 != $notto) {
				?>					<option value="<?php echo $value_2; ?>"<?php if(isset($selected[$value_2])) { ?> selected<?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo !$user ? get_post($value_2)->post_title : get_userdata($value_2)->user_login; ?></option>
<?php		} else if(is_array($value_2)) {
				?><optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $key_2; ?>">
<?php			foreach($value_2 AS $value_3) {
					if($value_3 != $notto) {
						?>						<option value="<?php echo $value_3; ?>"<?php if(isset($selected[$value_3])) { ?> selected<?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo !$user ? get_post($value_3)->post_title : get_userdata($value_3)->user_login; ?></option>
<?php				}
				}
?>					</optgroup>
<?php		}
		}
?>				</optgroup><?php
	}
}

function io_save_kategorie($post_id, $obj, $type) {
	if(current_user_can('administrator')) {		
		if(isset($_POST['oberkategorie_txt']) && $_POST['oberkategorie_txt'] != "") {
			update_post_meta($post_id, "io_" . $type . "_ok", sanitize_text_field($_POST['oberkategorie_txt']));
		} else if(isset($_POST['oberkategorie_sel']) && $_POST['oberkategorie_sel'] != -1) {
			update_post_meta($post_id, "io_" . $type . "_ok", sanitize_text_field($_POST['oberkategorie_sel']));
		} else if($obj->oberkategorie != "") {
			delete_post_meta($post_id, "io_" . $type . "_ok");
		}

		if(isset($_POST['unterkategorie_txt']) && $_POST['unterkategorie_txt'] != "") {
			update_post_meta($post_id, "io_" . $type . "_uk", sanitize_text_field($_POST['unterkategorie_txt']));
		} else if(isset($_POST['unterkategorie_sel']) && $_POST['unterkategorie_sel'] != -1) {
			update_post_meta($post_id, "io_" . $type . "_uk", sanitize_text_field($_POST['unterkategorie_sel']));
		} else if($obj->unterkategorie != "") {
			delete_post_meta($post_id, "io_" . $type . "_uk");
		}
	}
}

function io_get_ids($array, $obj = false, $user = false) {
	if(!is_array($array)) {
		return;
	}
	$values = array();
	if(!empty($array)) {
		foreach($array AS $value) {
			$temp = "";
			if($obj && $user && $value->ID) {
				$temp = $value->ID;
			} elseif($obj) {
				$temp = $value->id;
			} else {
				$temp = sanitize_text_field($value);
			}
			$values[$temp] = $temp;
		}
	}
	return $values;
}

function io_add_del($new, $old, $id, $class, $method, $switch = false) {
	$new = $new == null ? array() : $new;
	$old = $old == null ? array() : $old;
	
	if($class == "User_Control") {
		$old = io_get_ids($old, true, true);
	} else {
		$old = io_get_ids($old, true);
	}
	$new = io_get_ids($new);
	
	$to_del = array_diff($old, $new);
	$to_add = array_diff($new, $old);
	
	if($switch) {
		if(!empty($to_del)) {
			foreach($to_del AS $user) {
				call_user_func($class.'::del'.$method, $user, $id);
			}
		}

		if(!empty($to_add)) {
			foreach($to_add AS $user) {
				call_user_func($class.'::add'.$method, $user, $id);
			}
		}
	} else {
		if(!empty($to_del)) {
			foreach($to_del AS $user) {
				call_user_func($class.'::del'.$method, $id, $user);
			}
		}

		if(!empty($to_add)) {
			foreach($to_add AS $user) {
				call_user_func($class.'::add'.$method, $id, $user);
			}
		}
	}
}

function io_filter($query, $names, $posttype) {
	if(current_user_can('administrator') && function_exists(get_current_screen)) {
		$screen = get_current_screen();
		if($screen->post_type == $posttype && $screen->id == "edit-" . $posttype && isset($_POST['filter_action'])) {
			$query->query_vars['meta_query'] = array();
			$query->query_vars['meta_query']['relation'] = 'OR';

			foreach($names AS $name) {
				foreach($_GET[$name] AS $key => $value) {
					$query->query_vars['meta_query'] = array_merge($query->query_vars['meta_query'], array(array(
						'key'		=> $name,
						'value'		=> $value
					)));
				}
			}
		}
	}
	return $query;
}