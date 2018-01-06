<?php
/*
	File: crons/fivemin.php
	Created: 6/15/2016 at 2:43PM Eastern Time
	Info: Runs the queries below every five minutes.
	Add queries of your own to have queries executed every five minutes
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
require_once('../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
//Mining refill
$db->query("UPDATE `mining` SET `miningpower`=`miningpower`+(`max_miningpower`/(5)) WHERE `miningpower`<`max_miningpower`");
$db->query("UPDATE `mining` SET `miningpower`=`max_miningpower` WHERE `miningpower`>`max_miningpower`");
//Brave Refill
$db->query("UPDATE `users` SET `brave`=`brave`+((`maxbrave`/10)+0.5) WHERE `brave`<`maxbrave`");
$db->query("UPDATE `users` SET `brave`=`maxbrave` WHERE `brave`>`maxbrave`");
//HP Refill
$db->query("UPDATE users SET hp=hp+(maxhp/2) WHERE hp<maxhp AND userid != 10");
$db->query("UPDATE users SET hp=maxhp WHERE hp>maxhp");
//Energy Refill
$db->query("UPDATE users SET energy=energy+(maxenergy/(6)) WHERE energy<maxenergy AND vip_days=0");
$db->query("UPDATE users SET energy=energy+(maxenergy/(3)) WHERE energy<maxenergy AND vip_days>0");
$db->query("UPDATE users SET energy=maxenergy WHERE energy>maxenergy");
//Will refill
$db->query("UPDATE users SET will=will+(maxwill/10) WHERE will<maxwill");
$db->query("UPDATE users SET will = maxwill WHERE will > maxwill");
?>
