<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#user_login").prop('readonly', true);
		
		$(".form-field:eq(0)").prop("id", "io_user_login");
		$(".form-field:eq(1)").prop("id", "io_user_email");
		$(".form-field:eq(2)").prop("id", "io_first_name");
		$(".form-field:eq(3)").prop("id", "io_last_name");
		$(".form-field:eq(4)").prop("id", "io_user_url");
		$(".form-field:eq(5)").prop("id", "io_password");
		$(".form-field:eq(6)").prop("id", "io_mail");
		$(".form-field:eq(7)").prop("id", "io_role");
		
		$("#io_user_login").before($("#io_user_email"));
		$("#io_user_email").after($("#<?php echo User_Util::ATTRIBUT_ART; ?>"));
		$("#<?php echo User_Util::ATTRIBUT_ART; ?>").after($("#io_first_name"));
		$("#io_first_name").after($("#io_last_name"));
		$("#io_last_name").after($("#io_orga_name"));
		$("#io_orga_name").after($("#io_ort"));
		$("#io_ort").after($("#io_landesverband"));
		$("#io_user_url").after($("#io_groups"));
		$("#io_groups").after($("#io_permissions"));
		
		var userLoginValue = "";
		var userLoginValueTmp = "";
		
		var userArtChange = function() {
			switch($("input[name='<?php echo User_Util::POST_ATTRIBUT_ART; ?>']:checked").val()) {
				case 'user':
				default:
					$("#io_first_name").show();
					$("#io_last_name").show();
					$("#io_orga_name").hide();
					$("#io_ort").hide();
					$("#io_landesverband").hide();
					break;
				case 'landesverband':
					$("#io_first_name").hide();
					$("#io_last_name").hide();
					$("#io_orga_name").hide();
					$("#io_ort").hide();
					$("#io_landesverband").show();
					break;
				case 'basisgruppe':
					$("#io_first_name").hide();
					$("#io_last_name").hide();
					$("#io_orga_name").hide();
					$("#io_ort").show();
					$("#io_landesverband").show();
					break;
				case 'organisatorisch':
					$("#io_first_name").hide();
					$("#io_last_name").hide();
					$("#io_orga_name").show();
					$("#io_ort").hide();
					$("#io_landesverband").hide();
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
			}
			
			switch($("input[name='<?php echo User_Util::POST_ATTRIBUT_ART; ?>']:checked").val()) {
				case 'user':
				default:
					userLoginValue = $("#<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>").val() + " " + $("#<?php echo User_Util::POST_ATTRIBUT_LAST_NAME; ?>").val();
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
			}
			
			$("#user_login").val(userLoginValue);
		};
		
		userArtChange();
		
		$("input[name='<?php echo User_Util::POST_ATTRIBUT_ART; ?>']:radio").change(function() {
			userArtChange();
		});
		
		$("#<?php echo User_Util::POST_ATTRIBUT_FIRST_NAME; ?>").keyup(function() {
			userNameKeyUp();
		});
		
		$("#<?php echo User_Util::POST_ATTRIBUT_LAST_NAME; ?>").keyup(function() {
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