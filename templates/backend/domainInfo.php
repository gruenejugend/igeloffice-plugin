<?php
	/*ini_set("display_errors", 1);
	error_reporting(E_ALL);

	MySQL_Proxy::create("", Domain_Util::TABLE_HOST, array(
		'id'			=> MySQL_Proxy::getNewID("", Domain_Util::TABLE_HOST, 'id'),
		'hostname'		=> 'test.gruene-jugend.de',
		'https'			=> 1,
		'active'		=> 1
	));*/


	echo $domain->host;
	echo '<br>';
?>

<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Domain:</th>
		<td width="70%"><input type="text" name="<?php echo Domain_Util::POST_ATTRIBUT_HOST; ?>" value="<?php echo $domain->host; ?>" size="20"></td>
	</tr>
	<tr>
		<th width="30%">Location:</th>
		<td width="70%"><input type="text" name="<?php echo Domain_Util::POST_ATTRIBUT_LOCATION; ?>" value="<?php echo $domain->location; ?>" size="20"></td>
	</tr>
	<tr>
		<th width="30%">Ziel:</th>
		<td width="70%"><input type="url" name="<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>" id="<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>" value="<?php echo $domain->target; ?>" size="20"></td>
	</tr>
	<tr>
		<th width="30%">Verwendungszweck:</th>
		<td width="70%">
			<select name="<?php echo Domain_Util::POST_ATTRIBUT_VERWENDUNGSZWECK; ?>" id="<?php echo Domain_Util::POST_ATTRIBUT_VERWENDUNGSZWECK; ?>" size="1">
				<?php
				foreach(Domain_Util::VZ_ARRAY AS $key => $name) {?>
					<option value="<?php echo $key; ?>"<?php
						if($domain->zweck == $key) {
							?> selected<?php
						}
						?>><?php echo $name; ?></option>
<?php
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th width="30%">Autor*in:</th>
		<td width="70%"><?php echo get_the_author($post->ID); ?></td>
	</tr>
</table>

<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() {
		var verwendungszweck = function() {
			$("#<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>").prop('readonly', true);

			<?php
				$pruef = 0;
				foreach (Domain_Util::VZ_ADRESS_ARRAY AS $key => $target) {
					if(!Domain_Control::isNotVM($key)) {
						if ($pruef != 0) {
							echo " else ";
						}

						echo "if($(\"#".Domain_Util::POST_ATTRIBUT_VERWENDUNGSZWECK."\").val() == \"".$key."\") {
";
						echo "			$(\"#".Domain_Util::POST_ATTRIBUT_TARGET."\").val(\"".$target."\");
";
						echo "			}";
						$pruef = 1;
					}
				}
			?> else {
				$("#<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>").val("");
				$("#<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>").prop('readonly', false);
			}
		};

		$("#<?php echo Domain_Util::POST_ATTRIBUT_VERWENDUNGSZWECK; ?>").change(function() {
			verwendungszweck();
		});
	});
</script>