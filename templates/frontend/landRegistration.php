	<p id="land_box">
		<label for="land">Bundesland:<br>
			<select name="land" id="land">
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
			<input type="hidden" name="user_art" value="<?php echo User_Util::USER_ART_LANDESVERBAND; ?>">
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
		var userNameKeyUp = function() {
			switch($("#land").val()) {
				case 'baden-wuerttemberg':
					landKurz = 'Baden-Württemberg';
					break;
				case 'bayern':
					landKurz = 'Bayern';
					break;
				case 'berlin':
					landKurz = 'Berlin';
					break;
				case 'brandenburg':
					landKurz = 'Brandenburg';
					break;
				case 'bremen':
					landKurz = 'Bremen';
					break;
				case 'hamburg':
					landKurz = 'Hamburg';
					break;
				case 'hessen':
					landKurz = 'Hessen';
					break;
				case 'mecklenburg-vorpommern':
					landKurz = 'Mecklenburg-Vorpommern';
					break;
				case 'niedersachsen':
					landKurz = 'Niedersachsen';
					break;
				case 'nordrhein-westfalen':
					landKurz = 'Nordrhein-Westfalen';
					break;
				case 'rheinland-pfalz':
					landKurz = 'Rheinland-Pfalz';
					break;
				case 'schleswig-holstein':
					landKurz = 'Schleswig-Holstein';
					break;
				case 'saarland':
					landKurz = 'Saarland';
					break;
				case 'sachsen':
					landKurz = 'Sachsen';
					break;
				case 'sachsen-anhalt':
					landKurz = 'Sachsen-Anhalt';
					break;
				case 'thueringen':
					landKurz = 'Thüringen';
					break;
				default:
					landKurz = '';
					break;
			}
			$("#user_login").val(landKurz);
		};
		
		$("#land").change(function() {
			userNameKeyUp();
		});
	});
</script>
