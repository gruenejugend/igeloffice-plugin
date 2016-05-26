	<p>
		<label for="user_art">Nutzungsart:<br>
			<label for="user_art_user">Normale*r Benutzer*in
				<input type="radio" name="user_art" id="user_art_user" class="input" value="<?php echo User_Util::USER_ART_USER; ?>"<?php echo $userArtValue[0]; ?>>
			</label>
			<label for="user_art_landesverband">Landesverband
				<input type="radio" name="user_art" id="user_art_landesverband" class="input" value="<?php echo User_Util::USER_ART_LANDESVERBAND; ?>"<?php echo $userArtValue[1]; ?>>
			</label>
			<label for="user_art_basisgruppe">Basisgruppe
				<input type="radio" name="user_art" id="user_art_basisgruppe" class="input" value="<?php echo User_Util::USER_ART_BASISGRUPPE; ?>"<?php echo $userArtValue[2]; ?>>
			</label>
			<?php
				if($_GET['erweitert'] == 1) {
			?>
			<label for="user_art_basisgruppe">Organisatorische*r Benutzer*in
				<input type="radio" name="user_art" id="user_art_organisatorisch" class="input" value="<?php echo User_Util::USER_ART_ORGANISATORISCH; ?>"<?php echo $userArtValue[3]; ?>>
			</label>
			<?php
				}
			?>
		</label>
	</p>
	<p id="first_name_box">
		<label for="first_name">Vorname:<br>
			<input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(wp_unslash($first_name)); ?>" size="25">
		</label>
	</p>
	
	<p id="last_name_box">
		<label for="first_name">Nachname:<br>
			<input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr(wp_unslash($last_name)); ?>" size="25">
		</label>
	</p>
	<p id="orga_name_box">
		<label for="last_name">Name:<br>
			<input name="orga_name" id="orga_name" class="input" value="" size="25" type="text">
		</label>
	</p>
	<p id="name_box">
		<label for="name">Ort:<br>
			<input type="text" name="name" id="name" class="input" value="<?php echo esc_attr(wp_unslash($name)); ?>" size="25">
		</label>
	</p>
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
		var userLoginValueTmp = "";
		
		var userArtChange = function() {
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					$("#first_name_box").show();
					$("#last_name_box").show();
					$("#orga_name_box").hide();
					$("#name_box").hide();
					$("#land_box").hide();
					break;
				case 'landesverband':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#orga_name_box").hide();
					$("#name_box").hide();
					$("#land_box").show();
					break;
				case 'basisgruppe':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#orga_name_box").hide();
					$("#name_box").show();
					$("#land_box").show();
					break;
				case 'organisatorisch':
					$("#first_name_box").hide();
					$("#last_name_box").hide();
					$("#orga_name_box").show();
					$("#name_box").hide();
					$("#land_box").hide();
					break
			}
			userNameKeyUp();
			$("#user_login").val(userLoginValue);
		};
		
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
			};
			
			switch($("input[name='user_art']:checked").val()) {
				case 'user':
				default:
					userLoginValue = $("#first_name").val() + " " + $("#last_name").val();
					break;
				case 'landesverband':
					userLoginValue = landKurz;
					break;
				case 'basisgruppe':
					userLoginValue = $("#name").val();
					break;
				case 'organisatorisch':
					userLoginValue = $("#orga_name").val();
					break;
			};
			
			$("#user_login").val(userLoginUmlauts(userLoginValue));
		};
		
		userArtChange();
		
		$("input[name='user_art']:radio").change(function() {
			userArtChange();
		});
		
		$("#first_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#last_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#orga_name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#name").keyup(function() {
			userNameKeyUp();
		});
		
		$("#land").change(function() {
			userNameKeyUp();
		});
	});
</script>