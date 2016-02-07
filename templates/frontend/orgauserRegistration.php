	<p style="display: block;" id="orga_name_box">
		<label for="last_name">Name:<br>
			<input name="orga_name" id="orga_name" class="input" value="" size="25" type="text">
			<input type="hidden" name="user_art" value="organisatorisch">
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
			userLoginValue = $("#orga_name").val();
			$("#user_login").val(userLoginValue);
		};
		
		$("#orga_name").keyup(function() {
			userNameKeyUp();
		});
	});
</script>