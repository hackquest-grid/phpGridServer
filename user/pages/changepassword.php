<?php
/******************************************************************************
 * phpGridServer
 *
 * GNU LESSER GENERAL PUBLIC LICENSE
 * Version 2.1, February 1999
 *
 */
?><center><h1>Change Password</h1></center><br/>
<?php
if(isset($_POST["changepassword"]))
{
	$authenticationService = getService("UserAuthentication");
	try
	{
		$newtoken = $authenticationService->authenticate($_SESSION["principalid"], md5($_POST["oldpassword"]), "30");
		$authInfoService->releaseToken($_SESSION["principalid"], $newtoken);
		$passwordisvalid = True;
	}
	catch(Exception $e)
	{
		$passwordisvalid = False;
	}
	if(!$passwordisvalid)
	{
		$errormessage = "Old password wrong";
	}
	else if($_POST["newpassword"] != $_POST["newpassword2"])
	{
		$errormessage = "Password mismatch";
	}
	else
	{
		try
		{
			$authInfoService = getService("AuthInfo");
			$authInfo = $authInfoService->getAuthInfo($_SESSION["principalid"]);
			$authInfo->Password = $_POST["newpassword"];
			$authInfoService->setAuthInfo($authInfo);
			echo "<center><p><span class=\"success\";>Password changed successfully</span></p></center>";
		}
		catch(Exception $e)
		{
			$errormessage = "Could not change password";
		}
	}
}

?>
<center>
<?php if(isset($errormessage)) echo "<p><span class=\"error\";>$errormessage</span></p>"; ?>
<table>
<tr>
<form action="/user/?page=changepassword" method="post">
<tr><th>Old password</th><td><input type="password" name="oldpassword" value=""/></td></tr>
<tr><th>New password</th><td><input type="password" name="newpassword" value=""/></td></tr>
<tr><th>New password again</th><td><input type="password" name="newpassword2" value=""/></td></tr>
<tr><th></th><td><input type="submit" name="changepassword" value="Change"/></td></tr>
</form>
</table>
</center>
