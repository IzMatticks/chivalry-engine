<?php
/*
	File:		secmarket.php
	Created: 	4/5/2016 at 4:44PM Eastern Time
	Info: 		Allows players to sell their Chivalry Tokens at
				their own prices, and to buy offers on the market.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the Chivalry Token Market while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
echo "<h3><i class='game-icon game-icon-cash'></i> Chivalry Tokens Market</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'add':
        add();
        break;
    case 'remove':
        remove();
        break;
    case 'buy':
        buy();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $api, $userid;
    echo "<a href='?action=add'>Add Your Own Listing</a><hr />
	<table class='table table-bordered table-striped'>
		<tr>
			<th>
				Listing Owner
			</th>
			<th>
				For Sale
			</th>
			<th>
				Cost (Total)
			</th>
			<th>
				Actions
			</th>
		</tr>";
    $q = $db->query("/*qc=on*/SELECT * FROM `sec_market` ORDER BY `sec_cost` ASC");
    while ($r = $db->fetch_row($q)) {
        $totalcost = $r['sec_total'] * $r['sec_cost'];
        if ($r['sec_user'] == $userid) {
            $a = "[<a href='?action=remove&id={$r['sec_id']}'>Remove Listing</a>]";
        } else {
            $a = "[<a href='?action=buy&id={$r['sec_id']}'>Buy</a>]";
        }
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['sec_user']}'>" . parseUsername($r['sec_user']) . "</a> [{$r['sec_user']}]
				</td>
				<td>
					" . number_format($r['sec_total']) . "
				</td>
				<td>
					" . number_format($r['sec_cost']) . " (" . number_format($totalcost) . ")
				</td>
				<td>
					{$a}
				</td>
			</tr>";
    }
    echo "</table>";
}

function buy()
{
    global $db, $h, $userid, $api, $ir;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to buy.", true, 'secmarket.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "Please specify an existent listing to buy.", true, 'secmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row();
    if ($r['sec_user'] == $userid) {
        alert('danger', "Uh Oh!", "You cannot buy your own listing.", true, 'secmarket.php');
        die($h->endpage());
    }
    $totalcost = $r['sec_cost'] * $r['sec_total'];
	$taxed=$totalcost-($totalcost*0.02);
    if ($api->UserHasCurrency($userid, 'primary', $totalcost) == false) {
        alert('danger', "Uh Oh!", "You do not have enough Copper Coins to buy this listing.", true, 'secmarket.php');
        die($h->endpage());
    }
    $api->SystemLogsAdd($userid, 'secmarket', "Bought {$r['sec_total']} Chivalry Tokens from the market for {$totalcost} Copper Coins.");
    $api->UserGiveCurrency($userid, 'secondary', $r['sec_total']);
    $api->UserTakeCurrency($userid, 'primary', $totalcost);
    $api->UserGiveCurrency($r['sec_user'], 'primary', $taxed);
    $api->GameAddNotification($r['sec_user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has bought your
        {$r['sec_total']} Chivalry Tokens offer from the market for a total of {$taxed}.");
    $db->query("DELETE FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    alert('success', "Success!", "You have bought {$r['sec_total']} Chivalry Tokens for {$totalcost} Copper Coins", true, 'secmarket.php');
    die($h->endpage());
}

function remove()
{
    global $db, $h, $userid, $api;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to remove.", true, 'secmarket.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to remove a non-existent listing.", true, 'secmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row();
    if (!($r['sec_user'] == $userid)) {
        alert('danger', "Uh Oh!", "You are trying to remove a lising you do not own.", true, 'secmarket.php');
        die($h->endpage());
    }
    $api->SystemLogsAdd($userid, 'secmarket', "Removed {$r['sec_total']} Chivalry Tokens from the market.");
    $api->UserGiveCurrency($userid, 'secondary', $r['sec_total']);
    $db->query("DELETE FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    alert('success', "Success!", "You have removed your listing for {$r['sec_total']} Chivalry Tokens from the market.", true, 'secmarket.php');
    die($h->endpage());
}

function add()
{
    global $db, $h, $userid, $api, $ir;
    if (isset($_POST['qty']) && isset($_POST['cost'])) {
        $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
        $_POST['cost'] = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : '';
        if (empty($_POST['qty']) || empty($_POST['cost'])) {
            alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting it.");
            die($h->endpage());
        }
        if (!($api->UserHasCurrency($userid, 'secondary', $_POST['qty']))) {
            alert('danger', "Uh Oh!", "You are trying to add more Chivalry Tokens than you currently have.");
            die($h->endpage());
        }
		if ($_POST['cost'] > 50000)
		{
			alert('danger', "Uh Oh!", "The pricing you set is too expensive.");
            die($h->endpage());
		}
		if ($_POST['cost'] < 1000)
		{
			alert('danger', "Uh Oh!", "The pricing you set is too cheap.");
            die($h->endpage());
		}
        $db->query("INSERT INTO `sec_market` (`sec_user`, `sec_cost`, `sec_total`)
					VALUES ('{$userid}', '{$_POST['cost']}', '{$_POST['qty']}');");
        $api->UserTakeCurrency($userid, 'secondary', $_POST['qty']);
        $api->SystemLogsAdd($userid, 'secmarket', "Added {$_POST['qty']} to the secondary market for {$_POST['cost']} Copper Coins each.");
        alert('success', "Success!", "You have added your {$_POST['qty']} Chivalry Tokens to the market for
		    {$_POST['cost']} Copper Coins each.", true, 'secmarket.php');
        die($h->endpage());
    } else {
        alert('info', "Information!", "Fill out this form completely to add your Chivalry Tokens to the market.", false);
        echo "
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						Selling
					</th>
					<td>
						<input type='number' name='qty' class='form-control' required='1' min='1' value='{$ir['secondary_currency']}' max='{$ir['secondary_currency']}'>
					</td>
				</tr>
				<tr>
					<th>
						Price (Each)*
					</th>
					<td>
						<input type='number' name='cost' class='form-control' required='1' min='200' max='50000' value='200'>
					</td>
				<tr>
				
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Add Listing'>
					</td>
				</tr>
			</table>
		</form>
		*=Price subject to 2% market fee.";
    }
}

$h->endpage();