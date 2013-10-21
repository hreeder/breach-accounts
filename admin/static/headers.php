<?php
require('../includes/config.php');
require('../includes/functions.php');
$startTime = microtime(true);

require($config['xenforo']['path'] . '/library/XenForo/Autoloader.php');
XenForo_Autoloader::getInstance()->setupAutoloader($config['xenforo']['path'] . '/library');

XenForo_Application::initialize($config['xenforo']['path'] . '/library', $config['xenforo']['path']);
XenForo_Application::set('page_start_time', $startTime);

// Not required if you are not using any of the preloaded data
$dependencies = new XenForo_Dependencies_Public();
$dependencies->preLoadData();

XenForo_Session::startPublicSession();

$visitor = XenForo_Visitor::getInstance();

if (!$visitor->getUserID()) {
	header('Location: http://www.breachlan.co.uk/account/notloggedin.php');
    die();
}
if (!$visitor->isMemberOf($config['xenforo']['adminUserGroups'])){
    header('Location: http://www.breachlan.co.uk/account/notadmin.php');
    die();
}

$visitor_userid = $visitor->getUserId();
$visitor_username = $visitor->get('username');
$visitor_email = $visitor->get('email');

/*$visitor_userid = 175;
$visitor_username = 'Skull';
$visitor_email = 'harry776@gmail.com';*/ 
?>
<!DOCTYPE html>
<html>
<head>
<title>BreachLAN Account | Admin</title>
<link rel="stylesheet" href="../css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="../css/breachlan.css" type="text/css">
<link rel="stylesheet" href="../css/datepicker.css" type="text/css">
<script src="../js/jquery.min.js" type="text/javascript"></script>
<script src="../js/bootstrap.js" type="text/javascript"></script>
<script src="../js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="../js/jquery.tablesorter.min.js" type="text/javascript"></script>
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="http://www.breachlan.co.uk/"></a>
				<ul class="nav">
					<li><a href="index.php">Admin Home</a></li>
				</ul>
				<ul class="nav pull-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Return <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="../">Account Panel</a></li>
							<li><a href="http://www.breachlan.co.uk/">Website</a></li>
							<li><a href="http://www.breachlan.co.uk/forums/">Forums</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
	<div id="main-area">
    