   <tr bgcolor="FFFFFF">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="486591">
    <td colspan="2"><font color="fefefe">&nbsp;<b>Authentication / Accounts</b></font></td>
   </tr>

   <?php $selected[$current_config["auth_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>Select which type of authentication you are using.</td>
    <td>
     <select name="newsettings[auth_type]">
      <option value="sql"<?php echo $selected["sql"]; ?>>SQL</option>
      <option value="ldap"<?php echo $selected["ldap"]; ?>>LDAP</option>
      <option value="mail"<?php echo $selected["mail"]; ?>>Mail</option>
      <option value="http"<?php echo $selected["http"]; ?>>HTTP</option>
      <option value="pam"<?php echo $selected["pam"]; ?>>PAM (Not Ready)</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <?php $selected[$current_config["account_repository"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>Select where you want to store/retrieve user accounts.</td>
    <td>
     <select name="newsettings[account_repository]">
      <option value="sql"<?php echo $selected["sql"]; ?>>SQL</option>
      <option value="ldap"<?php echo $selected["ldap"]; ?>>LDAP</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>Auto create account records for authenticated users:</td>
    <td><input type="checkbox" name="newsettings[auto_create_acct]" value="True"<?php echo ($current_config["auto_create_acct"]?" checked":""); ?>></td>
   </tr>

   <?php $selected[$current_config["acl_default"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>If no ACL records for user or any group the user is a member of: </td>
    <td>
     <select name="newsettings[acl_default]">
      <option value="grant"<?php echo $selected["grant"]; ?>>Grant Access</option>
      <option value="deny"<?php echo $selected["deny"]; ?>>Deny Access</option>
     </select>
    </td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP host:</td>
    <td><input name="newsettings[ldap_host]" value="<?php echo $current_config["ldap_host"]; ?>"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP context:</td>
    <td><input name="newsettings[ldap_context]" value="<?php echo $current_config["ldap_context"]; ?>" size="40"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP root dn:</td>
    <td><input name="newsettings[ldap_root_dn]" value="<?php echo $current_config["ldap_root_dn"]; ?>" size="40"></td>
   </tr>

   <tr bgcolor="e6e6e6">
    <td>LDAP root password:</td>
    <td><input name="newsettings[ldap_root_pw]" value="<?php echo $current_config["ldap_root_pw"]; ?>"></td>
   </tr>
   
   <?php $selected[$current_config["ldap_encryption_type"]] = " selected"; ?>
   <tr bgcolor="e6e6e6">
    <td>LDAP encryption type</td>
    <td>
     <select name="newsettings[ldap_encryption_type]">
      <option value="DES"<?php echo $selected["DES"]; ?>>DES</option>
      <option value="MD5"<?php echo $selected["MD5"]; ?>>MD5</option>
     </select>
    </td>
   </tr>
   <?php $selected = array(); ?>

   <tr bgcolor="e6e6e6">
    <td>Use cookies to pass sessionid:</td>
    <td><input type="checkbox" name="newsettings[usecookies]" value="True"<?php echo ($current_config["usecookies"]?" checked":""); ?>></td>
   </tr>
   
   <tr bgcolor="e6e6e6">
    <td>Enter some random text for app_session <br>encryption (requires mcrypt)</td>
    <td><input name="newsettings[encryptkey]" value="<?php echo $current_config["encryptkey"]; ?>" size="40"></td>
   </tr>
