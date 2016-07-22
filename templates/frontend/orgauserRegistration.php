<p style="display: block;" id="<?php echo User_Util::POST_ATTRIBUT_ORGA_NAME ?>_box">
		<label for="<?php echo User_Util::POST_ATTRIBUT_ORGA_NAME ?>">Name:<br>
			<input name="<?php echo User_Util::POST_ATTRIBUT_ORGA_NAME ?>" id="<?php echo User_Util::POST_ATTRIBUT_ORGA_NAME ?>" class="input" value="" size="25" type="text">
			<input type="hidden" name="<?php echo User_Util::POST_ATTRIBUT_ART; ?>" value="<?php echo User_Util::USER_ART_ORGANISATORISCH; ?>">
			<?php
			if (isset($_GET[User_Util::POST_ATTRIBUT_ERWEITERT]) && $_GET[User_Util::POST_ATTRIBUT_ERWEITERT] == 1) {
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
			userLoginValue = $("#<?php echo User_Util::POST_ATTRIBUT_ORGA_NAME ?>").val();
			$("#user_login").val(userLoginValue);
		};
		
		$("#<?php echo User_Util::POST_ATTRIBUT_ORGA_NAME ?>").keyup(function() {
			userNameKeyUp();
		});
	});
</script>