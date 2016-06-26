<h1>{$title}</h1>
{literal}
<style type="text/css">
<!--
.templatestyle      { border-style:none;font-family: verdana;font-size:8pt;background-color:#000000;color:#ff0000;}
-->
</style>
{/literal}

<form name="optiontemplate" action=options_save.php method=post>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">

<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_opt_chpass}</b></td>
	</tr>
	<tr bgcolor="{$color_line1}">
	  <td>{$l_opt_curpass}</td>
	  <td><input type=password name=oldpass size=32 maxlength={$maxlen_password} value=""></td>
	</tr>
	<tr bgcolor="{$color_line2}">
	  <td>{$l_opt_newpass}</td>
	  <td><input type=password name=newpass1 size=32 maxlength={$maxlen_password} value=""></td>
	</tr>
	<tr bgcolor="{$color_line1}">
	  <td>{$l_opt_newpagain}</td>
	  <td><input type=password name=newpass2 size=32 maxlength={$maxlen_password} value=""></td>
	</tr>
{if $allow_shipnamechange == 1}
	<tr bgcolor="{$color_line2}">
	  <td>{$l_opt_shipname}</td>
	  <td><input type="text" name="newshipname" size="32" maxlength="50" value="{$oldshipname}"></td>
	</tr>
{/if}
	<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_opt_userint}</b></td>
	</tr>
	<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_opt_lang}</b></td>
	</tr>
	<tr bgcolor="{$color_line1}">
	  <td>{$l_opt_select}</td>
	  <td><select name=newlang>{$lang_drop_down}</select></td>
	</tr>
	<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_opt_template}</b></td>
	</tr>
	<tr bgcolor="{$color_line1}">
	  <td>{$l_opt_select}</td>
	  <td><select name=newtemplate ONCHANGE="showinfo()">{$template_drop_down}</select></td>
	</tr>
<tr bgcolor="{$color_line2}">
	  <td colspan="2" align="center">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td align="center">
	  <textarea class='templatestyle' rows="10" cols="60" wrap="virtual" name="templateinfo">Author: {$template_author}

Email: {$template_email}

Website: {$template_website}

Description: {$template_description}</textarea> </td><td align="center"><a id="templatelink" href="{$template_picture}" target="_blank"><img id="templateimagesmall" src="{$template_picturesmall}" border=0 width=150 height=150></a></td>
</td></tr>
</table></td>
	</tr>
{literal}
<SCRIPT LANGUAGE="JavaScript">
<!--
var shortcut=document.optiontemplate;
var author=new Array()
{/literal}{$authorarray}{literal}

var email=new Array()
{/literal}{$emailarray}{literal}

var website=new Array()
{/literal}{$websitearray}{literal}

var descriptions=new Array()
{/literal}{$descriptionarray}{literal}

var pictures=new Array()
{/literal}{$picturearray}{literal}

var picturessmall=new Array()
{/literal}{$picturesmallarray}{literal}

function showinfo()
{
	shortcut.templateinfo.value= 'Author: ' + author[shortcut.newtemplate.selectedIndex] + '\n\nEmail: ' + email[shortcut.newtemplate.selectedIndex] + '\n\nWebsite: ' + website[shortcut.newtemplate.selectedIndex] + '\n\nDescription: ' + descriptions[shortcut.newtemplate.selectedIndex]
	shortcut.templateimagesmall.src= picturessmall[shortcut.newtemplate.selectedIndex]
	document.getElementById('templatelink').href = pictures[shortcut.newtemplate.selectedIndex]
}
// -->
</SCRIPT>
{/literal}
	<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_avatar}</b></td>
	</tr>
	<tr bgcolor="{$color_line1}">
	  <td>{$l_opt_select}</td>
	  <td bgcolor="#000000"><img src="images/avatars/{$avatar}">&nbsp;[<a href="options_avatar.php">{$l_set}</a>]</td>
	</tr>
	{if $showteamicon == 1}
	<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_opt_teamicon}</b></td>
	</tr>
	<tr bgcolor="{$color_line1}">
	  <td>{$l_opt_select}</td>
	  <td bgcolor="#000000"><img src="images/icons/{$teamicon}">&nbsp;[<a href="options_teamicon.php">{$l_set}</a>]</td>
	</tr>
	{/if}
	<tr bgcolor="{$color_line2}">
	  <td>{$l_opt_mapwidth}</td>
	  <td><input type=text name=map_width size=4 maxlength=3 value="{$map_width}"></td>
	</tr>
	{if $enable_profilesupport == 1}
	<tr bgcolor="{$color_header}">
	  <td colspan=2><b>{$l_opt_profiletitle}</b></td>
	</tr>
	<tr bgcolor="{$color_line1}">
		{if $registeredprofile == 0}
		  <td colspan=2>{$l_opt_profile}<a href="profile.php">{$l_here}</a>.</td>
		{else}
		  <td colspan=2>{$l_opt_profilereg} - {$l_opt_profilerereg}<a href="profile.php">{$l_here}</a>.</td>
		{/if}
	</tr>
	{/if}
			<tr>
<td colspan=2>
<input type=submit value={$l_opt_save}>

</form>
</td>
</tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    <td background="templates/{$templatename}images/g-mid-right.gif">&nbsp;</td>
  </tr>

</table>
