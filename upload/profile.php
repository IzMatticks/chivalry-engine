<?php
/*
	File:		profile.php
	Created: 	4/5/2016 at 12:23AM Eastern Time
	Info: 		Allows players to view a player's profile page. This
				displays information about their level, location,
				gender, cash, estate, etc.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (!$_GET['user']) {
    alert("danger", "Uh Oh!", "Please specify a user you wish to view.", true, 'index.php');
} else {
    $q =
        $db->query(
            "SELECT `u`.`userid`, `user_level`, `laston`, `last_login`,
                    `registertime`, `vip_days`, `username`, `gender`,
					`primary_currency`, `secondary_currency`, `level`, `class`,
					`display_pic`, `hp`, `maxhp`, `guild`,
                    `fedjail`, `bank`, `lastip`, `lastip`,
                    `loginip`, `registerip`, `staff_notes`, `town_name`,
                    `house_name`, `guild_name`, `fed_out`, `fed_reason`,
					`infirmary_reason`, `infirmary_out`, `dungeon_reason`, `dungeon_out`,
					`browser`, `os`, `screensize`, `description`
                    FROM `users` `u`
                    INNER JOIN `town` AS `t`
                    ON `u`.`location` = `t`.`town_id`
					LEFT JOIN `infirmary` AS `i`
					ON `u`.`userid` = `i`.`infirmary_user`
					LEFT JOIN `dungeon` AS `d`
					ON `u`.`userid` = `d`.`dungeon_user`
                    INNER JOIN `estates` AS `e`
                    ON `u`.`maxwill` = e.`house_will`
                    LEFT JOIN `guild` AS `g`
                    ON `g`.`guild_id` = `u`.`guild`
                    LEFT JOIN `fedjail` AS `f`
                    ON `f`.`fed_userid` = `u`.`userid`
					LEFT JOIN `userdata` AS `ud`
                    ON `ud`.`userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['user']}");

    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert("danger", "Uh Oh!", "The user you are trying to view does not exist, or has an account issue.", true, 'index.php');
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $lon = ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston']) : "Never";
        $ula = ($r['laston'] == 0) ? 'Never' : DateTime_Parse($r['laston']);
        $ull = ($r['last_login'] == 0) ? 'Never' : DateTime_Parse($r['last_login']);
        $sup = date('F j, Y g:i:s a', $r['registertime']);
		$mi=$db->query("SELECT * FROM `marriage_tmg` WHERE (`proposer_id` = {$r['userid']} OR `proposed_id` = {$r['userid']}) AND `together` = 1");
		if ($db->num_rows($mi) == 0)
		{
			$married="N/A";
		}
		else
		{
			$mt=$db->fetch_row($mi);
			if ($mt['proposer_id'] == $r['userid'])
			{
				$p1=$ir;
				$p2=$db->fetch_row($db->query("SELECT * FROM `users` WHERE `userid` = {$mt['proposed_id']}"));
				$event=$p2;
			}
			else
			{
				$p1=$db->fetch_row($db->query("SELECT * FROM `users` WHERE `userid` = {$mt['proposer_id']}"));
				$p2=$ir;
				$event=$p1;
			}
			$married="<a href='profile.php?user={$event['userid']}'>{$event['username']}</a>";
		}
        $displaypic = ($r['display_pic']) ? "<img src='{$r['display_pic']}' class='img-thumbnail img-fluid' width='250' height='250'>" : '';
        $user_name = ($r['vip_days']) ? "<span class='text-danger'>{$r['username']} <i class='fa fa-shield'
            data-toggle='tooltip' title='{$r['vip_days']} VIP Days remaining.'></i></span>" : $r['username'];
        $ref_q =
            $db->query(
                "SELECT COUNT(`referalid`)
                         FROM `referals`
                         WHERE `referal_userid` = {$r['userid']}");
        $ref = $db->fetch_single($ref_q);
        $db->free_result($ref_q);
        $friend_q =
            $db->query(
                "SELECT COUNT(`friend_id`)
                         FROM `friends`
                         WHERE `friender` = {$r['userid']}");
        $friend = $db->fetch_single($friend_q);
        $db->free_result($friend_q);
        $enemy_q =
            $db->query(
                "SELECT COUNT(`enemy_id`)
                         FROM `enemy`
                         WHERE `enemy_adder` = {$r['userid']}");
        $enemy = $db->fetch_single($enemy_q);
        $db->free_result($enemy_q);
        $CurrentTime = time();
        $r['daysold'] = DateTime_Parse($r['registertime'], false, true);
		
		//Submit comment
		if (isset($_POST['comment']))
		{
			$comment = $db->escape(nl2br(htmlentities(stripslashes($_POST['comment']), ENT_QUOTES, 'ISO-8859-1')));
			if (empty($comment))
			{
				alert('danger',"Uh Oh!","Please fill out the comment form before submitting.",false);
			}
			elseif (strlen($comment) > 500)
			{
				alert('danger',"Uh Oh!","Comments may not be longer than 500 characters.",false);
			}
			elseif ($userid == $_GET['user'])
			{
				alert('danger',"Uh Oh!","You cannot comment on your own profile.",false);
			}
			elseif (!isset($_POST['verf']) || !verify_csrf_code("comment", stripslashes($_POST['verf']))) 
			{
				alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.",false);
			}
			elseif ($api->UserBlocked($userid,$_GET['user']))
			{
				alert('danger', "Uh Oh!", "This user has you blocked. You cannot send messages to players that have you blocked.", false);
			}
			elseif (!permission("CanComment",$userid))
			{
				alert('danger', "Uh Oh!", "You do not have permission to comment on another player's profile.", false);
			}
			else
			{
				$db->query("INSERT INTO `comments` (`cRECEIVE`, `cSEND`, `cTIME`, `cTEXT`) VALUES ('{$_GET['user']}', '{$userid}', '" . time() . "', '{$comment}')");
				$api->GameAddNotification($_GET['user'],"{$ir['username']} has left a comment on your profile. Click <a href='profile.php?user={$_GET['user']}'>here</a> to view it.");
				alert('success',"Success!","Comment was posted successfully.",false);
				$justposted=1;
			}
		}
		
		//Delete comment
		if (isset($_GET['del']))
		{
			$comment = (isset($_GET['del']) && is_numeric($_GET['del'])) ? abs($_GET['del']) : '';
			if (empty($comment))
			{
				alert('danger',"Uh Oh!","Please select the comment you wish to delete.",false);
			}
			else
			{
				$dc=$db->query("SELECT * FROM `comments` WHERE `cID` = {$comment} AND `cRECEIVE` = {$userid}");
				if ($db->num_rows($dc) == 0)
				{
					alert('danger',"Uh Oh!","This comment does not exist, or does not belong to you.",false);
				}
				else
				{
					$db->query("DELETE FROM `comments` WHERE `cID` = {$comment}");
					alert('success',"Success!","Comment has been deleted successfully.",false);
				}
			}
		}

        $rhpperc = round($r['hp'] / $r['maxhp'] * 100);
        echo "<h3>{$user_name}'s Profile</h3>";
        ?>
		<div class="row">
			<div class="col-lg-3">
				<?php
        echo "{$displaypic}<br />
			{$r['username']} [{$r['userid']}]<br />
			Rank: {$r['user_level']}<br />
			Location: {$r['town_name']}<br />
			Level: " . number_format($r['level']) . "<br />
			Married: {$married}<br />";
        echo ($r['guild']) ? "Guild: <a href='guilds.php?action=view&id={$r['guild']}'>{$r['guild_name']}</a><br />" : '';
        echo "Health: " . number_format($r['hp']) . "/" . number_format($r['maxhp']) . "<br />";

        ?>
			</div>
			<div class="col-lg-9">
				<ul class="nav nav-tabs nav-justified">
				  <li class="active nav-item"><a class='nav-link' data-toggle="tab" href="#info"><?php echo "Physical Info"; ?></a></li>
				  <li class='nav-item'><a class='nav-link' data-toggle="tab" href="#actions"><?php echo "Actions"; ?></a></li>
				  <?php
				  if ($ir['vip_days'] > 0)
				  {
					?>
						<li class='nav-item'><a class='nav-link' data-toggle="tab" href="#vip"><?php echo "VIP Only"; ?></a></li>
					<?php
				  }
				  ?>
				  <li class='nav-item'><a class='nav-link' data-toggle="tab" href="#financial"><?php echo "Financial Info"; ?></a></li>
				  <?php
        if (!in_array($ir['user_level'], array('Member', 'NPC'))) {
            echo "<li class='nav-item'><a class='nav-link' data-toggle='tab' href='#staff'>Staff</a></li>";
        }
        ?>
				</ul>
				<br />
				<div class="tab-content">
				  <div id="info" class="tab-pane active">
					<p>
						<?php
        echo
        "
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>Sex</th>
								<td>{$r['gender']}</td>
							</tr>
							<tr>
								<th>Class</th>
								<td>{$r['class']}</td>
							</tr>
							<tr>
								<th>Registered</th>
								<td>{$sup}</td>
							</tr>
							<tr>
								<th>Last Active</th>
								<td>{$ula}</td>
							</tr>
							<tr>
								<th>Last Login</th>
								<td>{$ull}</td>
							</tr>
							<tr>
								<th>Age</th>
								<td>{$r['daysold']}</td>
							</tr>";
		if ($r['description']) {
            echo "
							<tr>
								<th>Player Description</th>
								<td>
									{$r['description']}
								</td>
							</tr>";
        }
        if (user_infirmary($r['userid'])) {
            echo "
							<tr>
								<th>Infirmary</th>
								<td>In the infirmary for " . TimeUntil_Parse($r['infirmary_out']) . ".<br />
								{$r['infirmary_reason']}<br />
								[<a href='infirmary.php?action=heal&user={$r['userid']}'>Heal User</a>]
								</td>
							</tr>";
        }
        if (user_dungeon($r['userid'])) {
            echo "
							<tr>
								<th>Dungeon</th>
								<td>In the dungeon for " . TimeUntil_Parse($r['dungeon_out']) . ".<br />
								{$r['dungeon_reason']}<br />
								[<a href='dungeon.php?action=bail&user={$r['userid']}'>Bail Out</a>]
								[<a href='dungeon.php?action=bust&user={$r['userid']}'>Bust Out</a>]
								</td>
							</tr>";
        }
        if ($r['fedjail']) {
            echo "
							<tr>
								<th>Federal Dungeon</th>
								<td>In the federal dungeon for " . TimeUntil_Parse($r['fed_out']) . ".<br />
								{$r['fed_reason']}
								</td>
							</tr>";
        }

        echo "</table>
					</p>
				  </div>
				  <div id='actions' class='tab-pane'>
                    <a href='inbox.php?action=compose&user={$r['userid']}' class='btn btn-primary'>Message {$r['username']}</a>
                    <br />
				    <br />
				    <a href='sendcash.php?user={$r['userid']}' class='btn btn-primary'>Send {$r['username']} Cash</a>
				    <br />
				    <br />
					<a href='attack.php?user={$r['userid']}' class='btn btn-danger'><i class='ra ra-crossed-swords'></i> Attack {$r['username']}</a>
					<br />
					<br />
					<a href='hirespy.php?user={$r['userid']}' class='btn btn-primary'>Spy On {$r['username']}</a>
					<br />
					<br />
					<a href='contacts.php?action=add&user={$r['userid']}' class='btn btn-primary'>Add {$r['username']} to Contact List</a>";
					?>
				  </div>
				  <div id="vip" class="tab-pane">
					<?php
						echo "<a href='theft.php?user={$r['userid']}' class='btn btn-primary'>Rob {$r['username']}</a>
						<br />
						<br />
						<a href='friends.php?action=add&ID={$r['userid']}' class='btn btn-primary'>Add {$r['username']} as Friend</a>
						<br />
						<br />
						<a href='enemy.php?action=add&ID={$r['userid']}' class='btn btn-primary'>Add {$r['username']} as Enemy</a>";
					?>
				  </div>
				  <div id="financial" class="tab-pane">
					<?php
        echo
            "
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>Copper Coins</th>
								<td> " . number_format($r['primary_currency']) . "</td>
							</tr>
							<tr>
								<th>Chivalry Tokens</th>
								<td>" . number_format($r['secondary_currency']) . "</td>
							</tr>
							<tr>
								<th>Estate</th>
								<td>{$r['house_name']}</td>
							</tr>
							<tr>
								<th>Referrals</th>
								<td>" . number_format($ref) . "</td>
							</tr>
							<tr>
								<th>Friends</th>
								<td>" . number_format($friend) . "</td>
							</tr>
							<tr>
								<th>Enemies</th>
								<td>" . number_format($enemy) . "</td>
							</tr>
						</table>";

        ?>
				  </div>
				  <?php
        echo '<div id="staff" class="tab-pane">';
        if (!in_array($ir['user_level'], array('Member', 'NPC'))) {
            $fg = json_decode(get_fg_cache("cache/{$r['lastip']}.json", "{$r['lastip']}", 65655), true);
            $log = $db->fetch_single($db->query("SELECT `log_text` FROM `logs` WHERE `log_user` = {$r['userid']} ORDER BY `log_id` DESC"));
            echo "<a href='staff/staff_punish.php?action=fedjail&user={$r['userid']}' class='btn btn-primary'>Fedjail</a>
                <a href='staff/staff_punish.php?action=forumban&user={$r['userid']}' class='btn btn-primary'>Forum Ban</a>";
            echo "<table class='table table-bordered'>
							<tr>
								<th width='33%'>Data</th>
								<th>Output</th>
							</tr>
							<tr>
								<td>Location</td>
								<td>{$fg['city']}, {$fg['state']}, {$fg['country']}, ({$fg['isocode']})</td>
							</tr>
							<tr>
								<td>Risk Level</td>
								<td>" . parse_risk($fg['risk_level']) . "</td>
							</tr>
							<tr>
								<td>Last Hit</td>
								<td>{$r['lastip']}</td>
							</tr>
							<tr>
								<td>Last Login</td>
								<td>{$r['loginip']}</td>
							</tr>
							<tr>
								<td>Sign Up</td>
								<td>{$r['registerip']}</td>
							</tr>
							<tr>
								<td>
									Last Action
								</td>
								<td>
									{$log}
								</td>
							</tr>
							<tr>
								<td>
									Browser/OS
								</td>
								<td>
									{$r['browser']}/{$r['os']}
								</td>
							</tr>
					</table>
					<form action='staff/staff_punish.php?action=staffnotes' method='post'>
						Staff Notes
						<br />
						<textarea rows='7' class='form-control' name='staffnotes'>"
                . htmlentities($r['staff_notes'], ENT_QUOTES, 'ISO-8859-1')
                . "</textarea>
						<br />
						<input type='hidden' name='ID' value='{$_GET['user']}' />
						<input type='submit' class='btn btn-primary' value='Update Notes' />
					</form>";
        }
        ?>
				  
				  </div>
				</div>
				<h3>Profile Comments</h3> <?php
				$cq=$db->query("SELECT * FROM `comments` WHERE `cRECEIVE` = {$_GET['user']}  ORDER BY `cTIME` DESC LIMIT 8");
				echo "<table class='table table-bordered'>";
				while ($cr = $db->fetch_row($cq))
				{

					$ci=$db->fetch_row($db->query("SELECT * FROM `users` WHERE `userid` = {$cr['cSEND']}"));
					$dp = ($ci['display_pic']) ? "<img src='{$ci['display_pic']}' class='img-thumbnail img-responsive' width='50' height='50'>" : '';
					echo "<tr>
					<td align='left' width='25%'>
					{$dp} 
						<a href='profile.php?user={$cr['cSEND']}'>{$ci['username']}</a><br />
						<small>" . DateTime_Parse($cr['cTIME']) . "";
						if ($userid == $_GET['user'])
						{
							echo "<br /><a href='profile.php?user={$userid}&del={$cr['cID']}'>Delete</a>";
						}
						echo"
					</td>
					
					<td>
						{$cr['cTEXT']}
					</td>
					</tr>";
				}
				echo "</table><br />";
				if ($userid != $_GET['user'] && empty($justposted))
				{
					if (!permission("CanComment",$userid))
					{
						alert('danger', "Uh Oh!", "You do not have permission to comment on another player's profile.", false);
					}
					else
					{
						$mb = $db->query("SELECT * FROM `mail_bans` WHERE `mbUSER` = {$userid}");
						if ($db->num_rows($mb) != 0)
						{ }
						else
						{
							$csrf=request_csrf_html('comment');
							echo"
							<form method='post'>
								<textarea class='form-control' name='comment' cols='40' rows='7'></textarea>
								{$csrf}
								<br />
								<input type='submit' value='Post Comment' class='btn btn-primary'>
							</form>";
						}
					}
				}
				?>
			</div>
		</div>
		<?php
    }
}
function parse_risk($risk_level)
{
    switch ($risk_level) {
        case 2:
            return "Spam";
        case 3:
            return "Open Public Proxy";
        case 4:
            return "Tor Node";
        case 5:
            return "Honeypot / Botnet / DDOS Attack";
        default:
            return "No Risk";
    }
}

$h->endpage();