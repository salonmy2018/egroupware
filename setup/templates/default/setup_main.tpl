<!-- begin setup_main.tpl -->
<!-- begin the db section -->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
	<td align="left" bgcolor="486591">
		<font color="fefefe">{db_step_text}</font>
	</td>
	<td align="right" bgcolor="486591">
		&nbsp;
	</td>
</tr>

{V_db_filled_block}
<!-- end the db section -->

<!-- begin the config section -->
<tr>
	<td align="left" bgcolor="486591">
		<font color="fefefe">{config_step_text}</font>
	</td>
	<td align="right" bgcolor="486591">
		&nbsp;
	</td>
</tr>
<tr>
	<td align="center">
		<img src="{config_status_img}" alt="{config_status_alt}" border="0">
	</td>
	<td>
		{config_table_data}
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
		{ldap_table_data}
	</td>
</tr>
<!-- end the config section -->
<!-- begin the lang section -->
<tr>
	<td align="left" bgcolor="486591">
		<font color="fefefe">{lang_step_text}</font>
	</td>
	<td align="right" bgcolor="486591">
		&nbsp;
	</td>
</tr>
<tr>
	<td align="center">
		<img src="{lang_status_img}" alt="{lang_status_alt}" border="0">
	</td>
	<td>
		{lang_table_data}
	</td>
</tr>
<!-- end the lang section -->
<!-- begin the apps section -->
<tr>
	<td align="left" bgcolor="486591">
		<font color="fefefe">{apps_step_text}</font>
	</td>
	<td align="right" bgcolor="486591">
		&nbsp;
	</td>
</tr>
<tr>
	<td align="center">
		<img src="{apps_status_img}" alt="{apps_status_alt}" border="0">
	</td>
	<td>
		{apps_table_data}
	</td>
</tr>
<!-- end the apps section -->

</table>
<!-- end setup_main.tpl -->
