<?php
/*
	File:		preferences.php
	Created: 	4/5/2016 at 12:22AM Eastern Time
	Info: 		Allows players to change settings about their account.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'namechange':
        name_change();
        break;
    case 'timechange':
        time_change();
        break;
    case 'pwchange':
        pw_change();
        break;
    case 'picchange':
        pic_change();
        break;
    case 'sigchange':
        sigchange();
        break;
    case 'sexchange':
        sexchange();
        break;
    case 'emailchange':
        emailchange();
        break;
    default:
        prefs_home();
        break;
}
function prefs_home()
{
    global $ir;
    echo "Welcome to your account settings, {$ir['username']}. Here you can change many options concerning your account.<br />
	<table class='table table-bordered'>
		<tbody>
			<tr>
				<td>
					<a href='?action=namechange'>Change Name</a>
				</td>
				<td>
					<a href='?action=pwchange'>Change Password</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=timechange'>Change Timezone</a>
				</td>
				<td>
					<a href='?action=emailchange'>Change Email Opt-Setting</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=picchange'>Change Display Picture</a>
				</td>
				<td>
					<a href='?action=sexchange'>Change Sex</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=sigchange'>Change Forum Signature</a>
				</td>
				<td>

				</td>
			</tr>
		</tbody>
	</table>";
}

function name_change()
{
    global $db, $ir, $userid, $h;
    if (empty($_POST['newname'])) {
        $csrf = request_csrf_html('prefs_namechange');
        echo "<br />
		<h3>Username Change</h3>
		Here you can change your name that is shown throughout the game.<br />
		<div class='form-group'>
		<form method='post'>
			<input type='text' class='form-control' minlength='3' maxlength='20' id='username' required='1' value='{$ir['username']}' name='newname' />
			<br />
			{$csrf}
			<input type='submit' class='btn btn-primary' value='Change Username' />
			</div>
		</form>";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_namechange', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $_POST['newname'] = (isset($_POST['newname']) && is_string($_POST['newname'])) ? stripslashes($_POST['newname']) : '';
        if (empty($_POST['newname'])) {
            alert('danger', "Uh Oh!", "Please fill out the form and try again.");
            die($h->endpage());
        } elseif (((strlen($_POST['newname']) > 20) OR (strlen($_POST['newname']) < 3))) {
            alert('danger', "Uh Oh!", "Usernames must be at least 3 characters in length, and a maximum of 20.");
            die($h->endpage());
        }
        $check_ex = $db->query('SELECT `userid` FROM `users` WHERE `username` = "' . $db->escape($_POST['newname']) . '"');
        if ($db->num_rows($check_ex) > 0) {
            alert('danger', "Uh Oh!", "The username you chose is already in use.");
            die($h->endpage());
        }
        $_POST['newname'] = $db->escape(htmlentities($_POST['newname'], ENT_QUOTES, 'ISO-8859-1'));
        $db->query("UPDATE `users` SET `username` = '{$_POST['newname']}'  WHERE `userid` = $userid");
        alert('success', "Success!", "You have changed your username to {$_POST['newname']}.", true, 'preferences.php');
    }
}

function time_change()
{
    global $db, $userid;
    echo "<h3>Change Timezone</h3>";
    // Much thanks to Tamas Pap from Stack Overflow for the list <3
    // https://stackoverflow.com/questions/4755704/php-timezone-list
    if (!isset($_POST['timezone'])) {
        echo "Please select your timezone. Please note the server runs on Unix Time, which is Greenwich Mean Time.
		<br />
		<form method='post'>
		<select name='timezone' class='form form-control' type='dropdown'>
			<option value='Pacific/Tongatapu'>(GMT+13:00) Nuku'alofa</option>
			<option value='Pacific/Auckland'>(GMT+12:00) Auckland</option>
			<option value='Asia/Magadan'>(GMT+11:00) Magadan</option>
			<option value='Australia/Sydney'>(GMT+10:00) Sydney</option>
			<option value='Australia/Darwin'>(GMT+9:30) Darwin</option>
			<option value='Asia/Tokyo'>(GMT+9:00) Tokyo</option>
			<option value='Australia/Perth'>(GMT+8:00) Perth</option>
			<option value='Asia/Bangkok'>(GMT+7:00) Bangkok</option>
			<option value='Asia/Rangoon'>(GMT+6:30) Rangoon</option>
			<option value='Asia/Novosibirsk'>(GMT+6:00) Novosibirsk</option>
			<option value='Asia/Katmandu'>(GMT+5:45) Kathmandu</option>
			<option value='Asia/Calcutta'>(GMT+5:30) Chennai</option>
			<option value='Asia/Karachi'>(GMT+5:00) Karachi</option>
			<option value='Asia/Kabul'>(GMT+4:30) Kabul</option>
			<option value='Asia/Muscat'>(GMT+4:00) Muscat</option>
			<option value='Asia/Tehran'>(GMT+3:30) Tehran</option>
			<option value='Europe/Moscow'>(GMT+3:00) Moscow</option>
			<option value='Europe/Bucharest'>(GMT+2:00) Bucharest</option>
			<option value='Europe/Berlin'>(GMT+1:00) Berlin</option>
			<option value='Europe/London'>(GMT) Greenwich Mean Time</option>
			<option value='Atlantic/Cape_Verde'>(GMT-1:00) Cape Verde Islands</option>
			<option value='America/Noronha'>(GMT-2:00) Mid-Atlantic</option>
			<option value='America/Godthab'>(GMT-3:00) Greenland</option>
			<option value='America/St_Johns'>(GMT-3:30) Newfoundland</option>
			<option value='America/Halifax'>(GMT-4:00) Atlantic Time</option>
			<option value='America/New_York'>(GMT-5:00) Eastern Time</option>
			<option value='America/Chicago'>(GMT-6:00) Central Time</option>
			<option value='America/Denver'>(GMT-7:00) Mountain Time</option>
			<option value='America/Los_Angeles'>(GMT-8:00) Pacific Time</option>
			<option value='America/Anchorage'>(GMT-9:00) Alaska</option>
			<option value='America/Adak'>(GMT-10:00) Hawaii</option>
			<option value='Pacific/Apia'>(GMT-11:00) Midway Island</option>
			<option value='Pacific/Wake'>(GMT-12:00) International Date Line West</option>
		</select>
		<br />
		<input type='submit' class='btn btn-primary' value='Change Timezone'>";
    } else {
        $TimeZoneArray = ["Pacific/Wake", "Pacific/Apia", "America/Adak", "America/Anchorage", "America/Los_Angeles",
            "America/Denver", "America/Chicago", "America/New_York", "America/Halifax", "America/Godthab", "America/Noronha",
            "Atlantic/Cape_Verde", "Europe/London", "Europe/Berlin", "Europe/Bucharest", "Europe/Moscow", "Asia/Tehran",
            "Asia/Muscat", "Asia/Kabul", "Asia/Karachi", "Asia/Calcutta", "Asia/Katmandu", "Asia/Novosibirsks",
            "America/Godthab", "Asia/Rangoon", "Asia/Bangkok", "Australia/Perth", "Asia/Tokyo", "Australia/Darwin",
            "Australia/Sydney", "Asia/Magadan", "Pacific/Auckland", "Pacific/Tongatapu"

        ];
        if (!in_array($_POST['timezone'], $TimeZoneArray)) {
            alert('danger', "Uh Oh!", "The timezone you've selected is not valid.");
        } else {
            alert('success', "Success!", "You have changed your timezone successfully", true, 'preferences.php');
            $db->query("UPDATE `users` SET `timezone` = '{$_POST['timezone']}' WHERE `userid` = {$userid}");
        }
    }
}

function pw_change()
{
    global $db, $ir, $h;
    if (empty($_POST['oldpw'])) {
        $csrf = request_csrf_html('prefs_changepw');
        echo "
	<h3>Password Change</h3>
	<hr />
	<form method='post'>
	<table class='table table-bordered'>
	<tr>
		<th>
			Current Password
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='oldpw' />
		</td>
	</tr>
	<tr>
		<th>
			New Password
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='newpw' />
		</td>
	</tr>
	<tr>
		<th>
			Confirm Password
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='newpw2' />
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<input type='submit' class='btn btn-primary' value='Update Password' />
		</td>
	</tr>
    	{$csrf}
    	
	</form>
	</table>
   	";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changepw', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $oldpw = stripslashes($_POST['oldpw']);
        $newpw = stripslashes($_POST['newpw']);
        $newpw2 = stripslashes($_POST['newpw2']);
        if (!verify_user_password($oldpw, $ir['password'])) {
            alert('danger', "Uh Oh!", "Invalid old password.");
        } else if ($newpw !== $newpw2) {
            alert('danger', "Uh Oh!", "New password and confirmation did not match.");
        } else {
            // Re-encode password
            $new_psw = $db->escape(encode_password($newpw));
            $db->query("UPDATE `users` SET `password` = '{$new_psw}' WHERE `userid` = {$ir['userid']}");
            alert('success', "Success!", "You password was updated successfully.", true, 'preferences.php');
        }
    }
}

function pic_change()
{
    global $db, $h, $userid, $ir;
    if (!isset($_POST['newpic'])) {
        $csrf = request_csrf_html('prefs_changepic');
        echo "
		<h3>Change Display Picture</h3>
		<hr />
		Your images must be externally hosted. Any images that are not 250x250 will be scaled accordingly.<br />
		New Picture Link<br />
		<form method='post'>
			<input type='url' required='1' name='newpic' class='form-control' value='{$ir['display_pic']}' />
				{$csrf}
			<br />
			<input type='submit' class='btn btn-primary' value='Change Display Picture' />
		</form>
		";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changepic', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $npic = (isset($_POST['newpic']) && is_string($_POST['newpic'])) ? stripslashes($_POST['newpic']) : '';
        if (!empty($npic)) {
            $sz = get_filesize_remote($npic);
            if ($sz <= 0 || $sz >= 1048576) {
                alert('danger', "Uh Oh!", "You picture's file size is too big. At maximum, picture file size can be 1MB.");
                $h->endpage();
                exit;
            }
            $image = (@isImage($npic));
            if (!$image) {
                alert('danger', "Uh Oh!", "The link you've input is not an image.");
                die($h->endpage());
            }
        }
        $img = htmlentities($_POST['newpic'], ENT_QUOTES, 'ISO-8859-1');
        alert('success', "Success!", "You have successfully updated your display picture to what's shown below.", true, 'preferences.php');
        echo "<img src='{$img}' width='250' height='250' class='img-thumbnail img-responsive'>";
        $db->query("UPDATE `users` SET `display_pic` = '" . $db->escape($npic) . "' WHERE `userid` = {$userid}");
    }
}

function sigchange()
{
    global $db, $ir, $userid, $h;
    if (isset($_POST['sig'])) {
        $_POST['sig'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['sig']))));
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changesig', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (strlen($_POST['sig']) > 1024) {
            alert('danger', "Uh Oh!", "Your signature can only be, at maximum, 1,024 characters.");
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `signature` = '{$_POST['sig']}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "Your signature has been updated successfully.", true, 'preferences.php');
    } else {
        $ir['signature'] = strip_tags(stripslashes($ir['signature']));
        $csrf = request_csrf_html('prefs_changesig');
        echo "<form method='post'>
		<table class='table-bordered table'>
			<tr>
				<th colspan='2'>
					You can change your forum signature here. BBCode is allowable.
				</th>
			</tr>
			<tr>
				<th>
					Your Signature
				</th>
				<td>
					<textarea class='form-control' rows='4' name='sig'>{$ir['signature']}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Signature' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function sexchange()
{
    global $db, $userid, $ir, $h;
    if (isset($_POST['gender'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changesex', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female')) {
            alert('danger', "Uh Oh!", "You cannot change into an invalid sex.");
            die($h->endpage());
        }
        if ($ir['gender'] == $_POST['gender']) {
            alert('danger', "Uh Oh!", "You cannot turn yourself  back into your current sex.");
            die($h->endpage());
        }
        $e_gender = $db->escape(stripslashes($_POST['gender']));
        $db->query("UPDATE `users` SET `gender` = '{$e_gender}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have successfully changed your sex into {$_POST['gender']}.", true, 'preferences.php');
    } else {
        $g = ($ir['gender'] == "Male") ?
            $g = "	<option value='Male'>Male</option>
					<option value='Female'>Female</option>" :
            $g = "	<option value='Female'>Male</option>
					<option value='Male'>Female</option>";
        $csrf = request_csrf_html('prefs_changesex');
        echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Use this form to change your sex.
			</th>
		</tr>
		<tr>
			<th>
				Sex
			</th>
			<td>
				<select name='gender' class='form-control' type='dropdown'>
					{$g}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' value='Change Sex' class='btn btn-primary'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
    }
}

function emailchange()
{
    global $db, $userid, $ir, $h;
    if (isset($_POST['opt'])) {
        $_POST['opt'] = (isset($_POST['opt']) && is_numeric($_POST['opt'])) ? abs($_POST['opt']) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changeopt', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (!isset($_POST['opt']) || ($_POST['opt'] != 1 && $_POST['opt'] != 0)) {
            alert('danger', "Uh Oh!", "Invalid opt setting specified.");
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `email_optin` = {$_POST['opt']} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have changed your email opt setting.", true, 'preferences.php');
    } else {
        $g = ($ir['email_optin'] == 0) ?
            $g = "	<option value='1'>Opt-In</option>
					<option value='0'>Opt-Out</option>" :
            $g = "	<option value='0'>Opt-Out</option>
					<option value='1'>Opt-In</option>";
        $csrf = request_csrf_html('prefs_changeopt');
        $optsetting = ($ir['email_optin'] == 1) ? "Opt-in" : "Opt-out";
        echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Use this form to opt-in or out of emails from the game. You are currently {$optsetting} for game emails.
			</th>
		</tr>
		<tr>
			<th>
				Opt-Setting
			</th>
			<td>
				<select name='opt' class='form-control' type='dropdown'>
					{$g}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' value='Change Opt-Setting' class='btn btn-primary'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
    }
}

$h->endpage();