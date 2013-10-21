<?php
require('includes/config.php');
require('includes/functions.php');
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
if (!$visitor->isMemberOf(array(3,5,18))){
    header('Location: http://www.breachlan.co.uk/account/notadmin.php');
    die();
}
$admin = false;
if ($visitor->isMemberOf($config['xenforo']['adminUserGroups'])) {
    $admin = true;
}

$visitor_userid = $visitor->getUserId();
$visitor_username = $visitor->get('username');
$visitor_email = $visitor->get('email');

/*$visitor_userid = 175;
$visitor_username = 'Skull';
$visitor_email = 'harry776@gmail.com';*/

require('headers.html.php');
?>