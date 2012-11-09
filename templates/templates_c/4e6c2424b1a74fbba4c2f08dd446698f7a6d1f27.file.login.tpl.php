<?php /* Smarty version Smarty-3.1.12, created on 2012-11-05 09:11:20
         compiled from "/usr/local/zend/apache2/htdocs/CSCC01/templates/templates/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1333390545094c1a94bd871-29371057%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4e6c2424b1a74fbba4c2f08dd446698f7a6d1f27' => 
    array (
      0 => '/usr/local/zend/apache2/htdocs/CSCC01/templates/templates/login.tpl',
      1 => 1352124663,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1333390545094c1a94bd871-29371057',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5094c1a94d0041_73977191',
  'variables' => 
  array (
    'javascript' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5094c1a94d0041_73977191')) {function content_5094c1a94d0041_73977191($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link href="css/styles.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.9.1.custom.css">
<script src="js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.1.custom.min.js" type="text/javascript" ></script>
<?php echo $_smarty_tpl->tpl_vars['javascript']->value;?>

<script src="js/script.js" type="text/javascript"></script>
<title>Tutoring</title>
</head>

<body>
<div id="header">
  <div align="right">
    <form id="login" name="login" method="post" action="">
      <table width="200" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><label>Email</label><span id="email-error" hidden="true" class="error"> Please enter a valid email.</span></td>
          <td style="padding-left: 10px"><label>Password</label><span id="password-error" hidden="true" class="error"> Please enter a password.</span></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><input id="userlogin" type="email" tabindex="1" autofocus value="" name="userlogin" required></td>
          <td style="padding-left: 10px"><input id="password" type="password" tabindex="2" value="" name="password" required></td>
          <td valign="middle" style="padding-left: 10px">
          <input type="submit" name="signin" id="signin" value="Sign In" tabindex="2"></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="login-main">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center" valign="middle"><img src="images/uoft1.jpg" alt="" width="518" height="208"></td>
      <td><form id="signup" name="signup" method="post" action="">
        <table width="200" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2"><h1>Sign Up</h1></td>
          </tr>
          <tr>
            <td colspan="2"><label>First Name</label>
              </td>
          </tr>
          <tr>
            <td colspan="2"><span style="padding-top: 40px">
              <input type="text" tabindex="4" maxlength="20" size="55" autocomplete="on" id="firstname" value="" name="firstname" required>
            </span></td>
          </tr>
          <tr>
            <td colspan="2"><label>Last Name</label></td>
          </tr>
          <tr>
            <td colspan="2"><span style="padding-top: 40px">
              <input type="text" tabindex="5" maxlength="20" size="55" autocomplete="on" id="lastname" value="" name="lastname" required>
            </span></td>
          </tr>
          <tr>
            <td colspan="2"><label>Email</label><span id="email-form-error" hidden="true" class="error"> Please enter a valid email.</span></td>
          </tr>
          <tr>
            <td colspan="2"><span style="padding-top: 40px">
              <input type="email" data-ime-mode-disabled="" tabindex="6" maxlength="128" size="55" autocomplete="on" id="email" value="" name="email" required placeholder="me@utoronto.ca" >
            </span></td>
          </tr>
          <tr>
            <td colspan="2"><label>Password</label><span id="password-form-error" hidden="true" class="error"> Please enter a valid password.</span></td>
            </tr>
          <tr>
            <td><span style="padding-top: 40px">
              <input type="password" tabindex="7" id="passwd" value="" name="passwd" required>
            </span></td>
            <td><ol id="account_type">
              <li id="li-student" value="1" class="ui-state-default ui-selected">Student</li>
              <li id="li-tutor" value="2" class="ui-state-default">Tutor
                <input type="hidden" name="acct-type" id="acct-type" value="st">
              </li>
            </ol></td>
          </tr>
          <tr>
            <td colspan="2"><span style="padding-top: 40px">
              <input type="submit" name="join" id="join" value="Join Now" tabindex="8">
            </span></td>
          </tr>
        </table>
      </form></td>
    </tr>
  </table>
</div>
</body>
</html>
<?php }} ?>