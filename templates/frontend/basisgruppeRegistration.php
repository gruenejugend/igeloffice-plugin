	<p id="name_box">
		<label for="name">Ort:<br>
			<input type="text" name="<?php echo User_Util::POST_ATTRIBUT_NAME; ?>"
				   id="<?php echo User_Util::POST_ATTRIBUT_NAME; ?>" class="input"
				   value="<?php echo esc_attr(wp_unslash($name)); ?>" size="25">
			<input type="hidden" name="<?php echo User_Util::POST_ATTRIBUT_ART; ?>" value="<?php echo User_Util::USER_ART_BASISGRUPPE; ?>">
			<?php
				if(isset($_GET['erweitert']) && $_GET['erweitert'] == 1) {
					?><input type="hidden" name="erweitert" value="1">
<?php
				}
			?>
		</label>
	</p>
	<p id="land_box">
		<label for="<?php echo User_Util::POST_ATTRIBUT_LAND; ?>">Bundesland:<br>
			<select name="<?php echo User_Util::POST_ATTRIBUT_LAND; ?>" id="<?php echo User_Util::POST_ATTRIBUT_LAND; ?>">
				<option value="0">--- Bitte auswählen ---</option>
				<option<?php echo $landChecked[0];  ?> value="baden-wuerttemberg">Baden-Württemberg</option>
				<option<?php echo $landChecked[1];  ?> value="bayern">Bayern</option>
				<option<?php echo $landChecked[2];  ?> value="berlin">Berlin</option>
				<option<?php echo $landChecked[3];  ?> value="brandenburg">Brandenburg</option>
				<option<?php echo $landChecked[4];  ?> value="bremen">Bremen</option>
				<option<?php echo $landChecked[5];  ?> value="hamburg">Hamburg</option>
				<option<?php echo $landChecked[6];  ?> value="hessen">Hessen</option>
				<option<?php echo $landChecked[7];  ?> value="mecklenburg-vorpommern">Mecklenburg Vorpommern</option>
				<option<?php echo $landChecked[8];  ?> value="niedersachsen">Niedersachsen</option>
				<option<?php echo $landChecked[9];  ?> value="nordrhein-westfalen">Nordrhein-Westfalen</option>
				<option<?php echo $landChecked[10]; ?> value="rheinland-pfalz">Rheinland-Pfalz</option>
				<option<?php echo $landChecked[11]; ?> value="saarland">Saarland</option>
				<option<?php echo $landChecked[12]; ?> value="sachsen">Sachsen</option>
				<option<?php echo $landChecked[13]; ?> value="sachsen-anhalt">Sachsen-Anhalt</option>
				<option<?php echo $landChecked[14]; ?> value="schleswig-holstein">Schleswig-Holstein</option>
				<option<?php echo $landChecked[15]; ?> value="thueringen">Thüringen</option>
			</select>
		</label>
	</p>
	
<?php include 'js/general.php'; ?>

<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		var userLoginValue = "";
		
		var userNameKeyUp = function() {
			userLoginValue = $("#<?php echo User_Util::POST_ATTRIBUT_NAME; ?>").val();
			$("#user_login").val(userLoginValue);
		};

		$("#<?php echo User_Util::POST_ATTRIBUT_NAME; ?>").keyup(function () {
			userNameKeyUp();
		});
	});
</script>
