<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<th width="30%">Ziel der Domain:</th>
		<td width="70%">https://<input type="text" name="<?php echo Domain_Util::POST_ATTRIBUT_TARGET; ?>" value="<?php echo $domain->target; ?>" size="20"></td>
	</tr>
	<tr>
		<th width="30%">Zus&auml;tzlicher WWW-Alias:</th>
		<td width="70%"><input type="checkbox" name="<?php echo Domain_Util::POST_ATTRIBUT_ALIAS; ?>" value="1"<?php if(str_replace(".gruene-jugend.de", "", $domain->host) != $host) { ?> readonly="true"<?php } ?>></td>
	</tr>
</table>

<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#post_title").prop('readonly', true);
	});
</script>