	<p id="first_name_box">
		<label for="<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>">Vorname:<br>
			<input type="text" name="<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>" id="<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>" class="input" value="<?php echo esc_attr(wp_unslash($first_name)); ?>" size="25">
		</label>
	</p>
	
	<p id="last_name_box">
		<label for="last_name">Nachname:<br>
			<input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr(wp_unslash($last_name)); ?>" size="25">
			<input type="hidden" name="<?php echo User_Util::POST_ATTRIBUT_ART; ?>" value="<?php echo User_Util::USER_ART_USER; ?>">
			<?php
				if(isset($_GET['erweitert']) && $_GET['erweitert'] == 1) {
					?><input type="hidden" name="erweitert" value="1">
<?php
				}
			?>
		</label>
	</p>
	
<?php include 'js/general.php'; ?>

<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		var userLoginValue = "";
		
		var userNameKeyUp = function() {
			userLoginValue = $("#<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>").val() + " " + $("#last_name").val();
			$("#user_login").val(userLoginValue);
		};
		
		$("#<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>").keyup(function() {
			userNameKeyUp();
		});
		
		$("#last_name").keyup(function() {
			userNameKeyUp();
		});
	});
</script>