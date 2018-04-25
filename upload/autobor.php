<?php
require('globals.php');
if ($ir['bor'] == 0)
{
    alert('danger',"Uh Oh!","You cannot open anymore Boxes of Random today.",true,'explore.php');
    die($h->endpage());
}
if ($ir['autobor'] == 0)
{
	alert('danger',"Uh Oh!","You need to have an Auto Box of Random Opener redeemed on your account to use this feature.",true,'explore.php');
	die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon'))
{
    alert('danger',"Uh Oh!","You cannot open Boxes of Random while in the dungeon.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot open Boxes of Random while in the infirmary.",true,'explore.php');
    die($h->endpage());
}
if (!$api->UserHasItem($userid,33,1))
{
    alert('danger',"Uh Oh!","You need at least one Box of Random to open Boxes of Random.",true,'explore.php');
    die($h->endpage());
}
if (isset($_POST['open']))
{
	$_POST['open'] = abs($_POST['open']);
	if (empty($_POST['open']))
	{
		alert('danger',"Uh Oh!","Please specify how many Boxes of Random you would like to open using this system.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['bor'])
	{
		alert('danger',"Uh Oh!","You do not have that many Boxes of Random available to you at this moment.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['autobor'])
	{
		alert('danger',"Uh Oh!","You are trying to open more Boxes of Random than you currently have redeemed on your account.");
		die($h->endpage());
	}
	if (!$api->UserHasItem($userid,33,$_POST['open']))
	{
		alert('danger',"Uh Oh!","You do not have that many Boxes of Random in your inventory to open that many.");
		die($h->endpage());
	}
	$number=0;
	$copper=0;
	$tokens=0;
	$infirmary=0;
	$bread=0;
	$venison=0;
	$potion=0;
	$wraps=0;
	$keys=0;
	$explosives=0;
	$gymscroll=0;
	$attackscroll=0;
    $mystery=0;
    $needle=0;
	$hexbags=0;
	$rickitybomb=0;
	$nothing=0;
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,91);
		if ($chance <= 30)
		{
			$cash=Random(750,3000);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$copper=$copper+$cash;
		}
		elseif (($chance > 30) && ($chance <= 40))
		{
			$cash=Random(10,25);
			$specialnumber=((getSkillLevel($userid,11)*5)/100);
			$cash=round($cash+($cash*$specialnumber));
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$tokens=$tokens+$cash;
		}
		elseif (($chance > 40) && ($chance <= 50))
		{
			$cash=Random(5,15);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$infirmary=$infirmary+$cash;
		}
		elseif (($chance > 50) && ($chance <= 55))
		{
			$bread=$bread+1;
		}
		elseif (($chance > 55) && ($chance <= 60))
		{
			$venison=$venison+1;
		}
		elseif (($chance > 60) && ($chance <= 65))
		{
			$potion=$potion+1;
		}
		elseif (($chance > 65) && ($chance <= 70))
		{
			$rng=Random(2,4);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$wraps=$wraps+$rng;
		}
		elseif (($chance > 70) && ($chance <= 75))
		{
			$rng=Random(2,4);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$keys=$keys+$rng;
		}
		elseif (($chance > 75) && ($chance <= 83))
		{
			$rng=Random(1,2);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$explosives=$explosives+$rng;
		}
		elseif (($chance > 83) && ($chance <= 85))
		{
			$gymscroll=$gymscroll+1;
		}
		elseif (($chance > 85) && ($chance <= 86))
		{
			$attackscroll=$attackscroll+1;
		}
        elseif (($chance > 86) && ($chance <= 87))
		{
			$mystery=$mystery+1;
		}
        elseif (($chance > 87) && ($chance <= 88))
		{
			$needle=$needle+1;
		}
		elseif ($chance == 89)
		{
			if (Random(1,10) == 9)
			{
				$rickitybomb=$rickitybomb+1;
			}
			else
			{
				$nothing=$nothing+1;
			}
		}
		elseif ($chance == 90)
		{
			if (Random(1,10) == 9)
			{
				$hexbags=$hexbags+Random(1,3);
			}
			else
			{
				$nothing=$nothing+1;
			}
		}
		else
		{
			$nothing=$nothing+1;
		}
	}
	$db->query("UPDATE `users` SET `bor` = `bor` - {$_POST['open']} WHERE `userid` = {$userid}");
	$db->query("UPDATE `user_settings` SET `autobor` = `autobor` - {$_POST['open']} WHERE `userid` = {$userid}");
	echo "After automatically opening {$number} Boxes of Random, you have gained the following:<br />
		{$copper} Copper Coins.<br />
		{$tokens} Chivalry Tokens.<br />
		{$infirmary} minutes in the infirmary.<br />
		{$wraps} Linen Wraps<br />
		{$keys} Dungeon Keys.<br />
		{$bread} Bread.<br />
		{$venison} Venison.<br />
		{$potion} Small Health Potion(s).<br />
		{$explosives} Small Explosive(s).<br />
		{$gymscroll} Chivalry Gym Pass(s)<br />
		{$attackscroll} Distant Attack Scroll(s)<br />
        {$needle} Acupuncture Needle(s)<br />
        {$mystery} Mysterious Potion(s)<br />
		{$hexbags} extra Hexbag(s)<br />
		{$rickitybomb} Rickity Bomb(s)<br />
		{$nothing} Boxes of Random had nothing in them.";
	$api->UserGiveCurrency($userid,'primary',$copper);
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	$api->UserStatusSet($userid,'infirmary',$infirmary,"Ticking Box");
	$api->UserGiveItem($userid,6,$wraps);
	$api->UserGiveItem($userid,30,$keys);
	$api->UserGiveItem($userid,19,$bread);
	$api->UserGiveItem($userid,20,$venison);
	$api->UserGiveItem($userid,7,$potion);
	$api->UserGiveItem($userid,28,$explosives);
	$api->UserGiveItem($userid,18,$gymscroll);
	$api->UserGiveItem($userid,90,$attackscroll);
    $api->UserGiveItem($userid,123,$mystery);
    $api->UserGiveItem($userid,100,$needle);
	$api->UserGiveItem($userid,149,$rickitybomb);
	$db->query("UPDATE `users` SET `hexbags` = `hexbags` + {$hexbags} WHERE `userid` = {$userid}");
	$api->UserTakeItem($userid,33,$_POST['open']);
	//Logs here
	$api->SystemLogsAdd($userid,"bor","Received {$nothing} nothing(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$needle} Acupuncture Needle(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$mystery} Mysterious Potion(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$attackscroll} Distant Attack Scroll(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$gymscroll} Chivalry Gym Pass(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$explosives} Small Explosives.");
	$api->SystemLogsAdd($userid,"bor","Received {$keys} Dungeon Keys.");
	$api->SystemLogsAdd($userid,"bor","Received {$wraps} Linen Wraps.");
	$api->SystemLogsAdd($userid,"bor","Received {$infirmary} infirmary.");
	$api->SystemLogsAdd($userid,"bor","Received {$copper} Copper Coins.");
	$api->SystemLogsAdd($userid,"bor","Received {$tokens} Chivalry Tokens.");
	$api->SystemLogsAdd($userid,"bor","Received {$bread} Bread(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$venison} Venison.");
	$api->SystemLogsAdd($userid,"bor","Received {$potion} Small Health Potion(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$hexbags} Hexbag(s).");
	$api->SystemLogsAdd($userid,"bor","Received {$rickitybomb} Rickity Bomb(s).");
	
}
else
{
	$maxnumber = ($ir['autobor'] > $ir['bor']) ? $ir['bor'] : $ir['autobor'] ;
	echo "How many Boxes of Random would you like to open in an automated fashion? You can open {$ir['bor']} Boxes of Random today. You 
	currently have {$ir['autobor']} uses left on your Auto Box of Random Opener.
	<br />
	<form method='post'>
		<input type='number' min='1' max='{$maxnumber}' name='open' class='form-control' required='1' value='{$maxnumber}'>
		<input type='submit' value='Open Boxes of Random' class='btn btn-primary'>
	</form>";
}
$h->endpage();