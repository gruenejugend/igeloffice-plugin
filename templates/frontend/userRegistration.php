	<p id="first_name_box">
		<label for="first_name">Vorname:<br>
			<input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(wp_unslash($first_name)); ?>" size="25">
		</label>
	</p>
	
	<p id="last_name_box">
		<label for="first_name">Nachname:<br>
			<input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr(wp_unslash($last_name)); ?>" size="25">
			<input type="hidden" name="user_art" value="user">
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
			userLoginValue = $("#first_name").val() + " " + $("#last_name").val();
			$("#user_login").val(userLoginValue);
		};
		
		$("#first_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#last_name").keyup(function() {
			userNameKeyUp();
		});
	});
</script>