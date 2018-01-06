<?php
/*
	File:		bomb.php
	Created: 	10/18/2017 at 10:49AM Eastern Time
	Info: 		Blow up your opponent.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "twg":
        twg();
        break;
    case "top100":
        top100();
        break;
    case "mgpoll":
        mgpoll();
        break;
	case "apex":
		apex();
		break;
    default:
        home();
        break;
}
function home()
{
    global $set, $h;
    echo "Here you may vote for {$set['WebsiteName']} at various RPG toplists and be rewarded. Whether or not you voted is
logged. If you scam this system, you will be dealt with severely. If you do not get rewarded, try voting again later.
<br />
<a href='?action=twg'>Vote at Top Web Games! (20,000 Copper Coins)</a><br />
<a href='?action=top100'>Vote at Top 100 Arena (25 Hexbags)</a><br />
<a href='?action=mgpoll'>Vote at MGPoll (10 Chivalry Tokens)</a><br />
<a href='?action=apex'>Vote at Apex Web Gaming (25 Boxes of Random)</a><br />";
    $h->endpage();
}
function twg()
{
    global $db,$userid,$api,$h;
    $q = $db->query("SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'twg'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at TWG today. If you vote again, you will not be rewarded.",true,"http://www.topwebgames.com/in.aspx?id=11600&uid={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://topwebgames.com/in.aspx?ID=8303&uid={$userid}");
        exit;
    }
}
function top100()
{
    global $db,$userid,$api,$h;
    $q = $db->query("SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'top100'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at Top 100 Arena today. If you vote again, you will not be rewarded.",true,"http://www.top100arena.com/in.asp?id=86377&incentive={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://www.top100arena.com/in.asp?id=86377&incentive={$userid}");
        exit;
    }
}

function mgpoll()
{
    global $db,$userid,$api,$h;
    $q = $db->query("SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'mgp'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at MGPoll today. If you vote again, you will not be rewarded.",true,"http://mgpoll.com/vote/260/{$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://mgpoll.com/vote/260/{$userid}");
        exit;
    }
}
function apex()
{
    global $db,$userid,$api,$h;
    $q = $db->query("SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'apex'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at Apex Web Gaming today. If you vote again, you will not be rewarded.",true,"http://apexwebgaming.com/index.php?a=in&u=TheMasterGeneral&i_id={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://apexwebgaming.com/index.php?a=in&u=TheMasterGeneral&i_id={$userid}");
        exit;
    }
}