<?php
class headers
{

    function startheaders()
    {
		global $ir, $set, $lang, $db, $NoHeader;
		?>
<!DOCTYPE html>
<html lang="en">
<head>
	<center>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<meta name="theme-color" content="#000000">
	<noscript>
		<meta http-equiv="refresh" content="0;url=no-script.php">
	</noscript>

    <?php echo "<title>Chivalry Engine</title>"; ?>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bs2.css" rel="stylesheet">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;
        /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
    }
	a {
		color: gray;
	}
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<?php
if (empty($NoHeader))
{
	?>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Chivalry Engine</a>
            </div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="explore.php"><?php echo $lang['MENU_EXPLORE']; ?></a>
                    </li>
                </ul>
					<ul class="nav navbar-nav navbar-right">
					<ul class="nav navbar-nav">
                    <li>
						<?php
						$ir['mail']=$db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
						$ir['notifications']=$db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
							echo "<a href='inbox.php'>{$lang['MENU_MAIL']} ({$ir['mail']})</a>";
						?>
                    </li>
                    <li>
                        <?php
							echo "<a href='notifications.php'>{$lang['MENU_EVENT']} ({$ir['notifications']})</a>";
						?>
                    </li>
                    <li>
                        <a href="inventory.php"><?php echo $lang['MENU_INVENTORY']; ?></a>
                    </li>
                </ul>
					<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<?php
				if (!$ir['display_pic'])
				{
					
				}
				else
				{
                    echo"<img src='{$ir['display_pic']}' width='24' height='24'>";
				}
					 
					echo" {$lang['GEN_GREETING']}, {$ir['username']}"; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="profile.php?user=<?php echo "{$ir['userid']}"; ?>"><i class="fa fa-fw fa-user"></i> <?php echo $lang['MENU_PROFILE']; ?></a>
                        </li>
                        <li>
                            <a href="preferences.php?action=menu"><i class="fa fa-fw fa-gear"></i> <?php echo $lang['MENU_SETTINGS']; ?></a>
                        </li>
						<?php
							if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant')))
							{
								?><li class="divider"></li>
								<li>
									<a href="staff/"><i class="fa fa-fw fa fa-server"></i> <?php echo $lang['MENU_STAFF']; ?></a>
								</li><?php
							}
						
						
						?>
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> <?php echo $lang['MENU_LOGOUT']; ?></a>
                        </li>
                    </ul>
                </li>
				</ul>
            <!-- Collect the nav links, forms, and other content for toggling -->
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <div class="row">
            <div class="col-lg-12 text-center">
		<?php
		if ($ir['mail'] > 0)
		{
			echo "<div class='alert alert-info'> <strong>{$lang['MENU_UNREADMAIL1']}</strong> {$lang['MENU_UNREADMAIL2']} {$ir['mail']} {$lang['MENU_UNREADMAIL3']} <a href='inbox.php'>{$lang["GEN_HERE"]}</a> {$lang['MENU_UNREADMAIL4']}</div>";
		}
		if ($ir['notifications'] > 0)
		{
			echo "<div class='alert alert-info'> <strong>{$lang['MENU_UNREADNOTIF']}</strong> {$lang['MENU_UNREADMAIL2']} {$ir['notifications']} {$lang['MENU_UNREADNOTIF1']} <a href='notifications.php'>{$lang["GEN_HERE"]}</a> {$lang['MENU_UNREADMAIL4']}</div>";
		}
		if ($ir['announcements'] > 0)
		{
			echo "<div class='alert alert-info'> <strong>{$lang['MENU_UNREADANNONCE']}</strong> {$lang['MENU_UNREADANNONCE1']} {$ir['announcements']} {$lang['MENU_UNREADANNONCE2']} <a href='announcements.php'>{$lang["GEN_HERE"]}</a>.</div>";
		}
		if (user_infirmary($ir['userid']) == true)
		{
			$CurrentTime=time();
			$InfirmaryOut=$db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
			$InfirmaryRemain=round((($InfirmaryOut - $CurrentTime) / 60), 2);
			echo "<div class='alert alert-info'> <strong>{$lang['MENU_INFIRMARY']}</strong> {$lang['MENU_INFIRMARY1']} {$InfirmaryRemain} {$lang['GEN_MINUTES']}.</div>";
		}
		date_default_timezone_set($ir['timezone']);  
		
	}
	}
	function userdata($ir, $lv, $fm, $cm, $dosessh = 1)
    {
		global $db, $c, $userid, $set;
		$IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$db->query(
                "UPDATE `users`
                 SET `laston` = {$_SERVER['REQUEST_TIME']}, `lastip` = '$IP'
                 WHERE `userid` = $userid");
		if (!$ir['email'])
        {
            global $domain;
            die(
                    "<body>Your account may be broken. Please mail help@{$domain} stating your username and player ID.");
        }
        if (!isset($_SESSION['attacking']))
        {
            $_SESSION['attacking'] = 'false';
        }
        if ($dosessh && ($_SESSION['attacking'] == 'true' || $ir['attacking'] == 'true'))
        {
            echo "You lost all your EXP for running from the fight.";
            $db->query(
                    "UPDATE `users`
                     SET `xp` = 0, `attacking` = 'false'
                     WHERE `userid` = $userid");
            $_SESSION['attacking'] = 'false';
        }
        $d = "";
        $u = $ir['username'];
        if ($ir['vip_days'])
        {
            $u = "<span style='color: red;'>{$ir['username']}</span>";
            $d =
                    "<img src='donator.gif'
                     alt='VIP: {$ir['vip_days']} Days Left'
                     title='VIP: {$ir['vip_days']} Days Left' />";
        }

        $gn = "";
        global $staffpage;

        $bgcolor = 'FFFFFF';
	}
	function endpage()
    {
        global $db, $ir, $lang;
        $query_extra = '';
        if (isset($_GET['mysqldebug']) && $ir['user_level'] == 2)
        {
            $query_extra = '<br />' . implode('<br />', $db->queries);
        }
		?>
		</div>
			</div>
        <!-- /.row -->

			</div>
			<!-- /.container -->

			<!-- jQuery Version 1.12.1 -->
			<script src="js/jquery-1.12.0.min.js"></script>

			<!-- Bootstrap Core JavaScript -->
			<script src="js/bootstrap.min.js"></script>
			<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
			<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
			<!-- Other JavaScript -->
			<script src="js/register.js"></script>
			<script src="js/game.js"></script>
		</body>
			<footer>
				<p>
					<br />
					<?php 
					echo "<hr />
					{$lang['MENU_TIN']}  
						" . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$lang['MENU_OUT']}";
					?>
					&copy; <?php echo date("Y");
					echo"<br/>{$db->num_queries} {$lang['MENU_QE']}.{$query_extra}";
					?>
				</p>
			</footer>
		</html>
			   <?php
			}
	
}