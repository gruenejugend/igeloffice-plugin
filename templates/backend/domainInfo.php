<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Domain:</th>
		<td width="70%"><input type="url" name="<?php echo Domain_Util::POST_ATTRIBUT_HOST; ?>" value="<?php echo $domain->host; ?>" size="20"></td>
	</tr>
	<tr>
		<th width="30%">Ziel:</th>
		<td width="70%"><input type="url" name="<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>" value="<?php echo $domain->target; ?>" size="20"></td>
	</tr>
	<tr>
		<th width="30%">Verwendungszweck:</th>
		<td width="70%">
			<select name="<?php echo Domain_Util::POST_ATTRIBUT_VERWENDUNGSZWECK; ?>" size="1">
				<?php
				foreach(Domain_Util::VZ_ARRAY AS $key => $name) {?>
					<option value="<?php echo $key; ?>"<?php if($domain->verwendungszweck == $key) {?> selected<?php} ?>><?php echo $name; ?></option>
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
		$("#post_title").prop('readonly', true);
	});
</script>