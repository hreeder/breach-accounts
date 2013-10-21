<?php
require('static/headers.php');
$conn = mysql_connect($config['database']['host'],$config['database']['user'],$config['database']['pass']);
mysql_select_db($config['database']['name']);
$eventID = mysql_real_escape_string($_GET['eventid']);

if (!isset($_GET['confirm'])) {
	$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_ID = " . $eventID;
	$result = mysql_query($sql);
	$event = mysql_fetch_array($result);
} else if (isset($_GET['confirm']) && $_GET['confirm'] == "true") {
	$sql = "UPDATE " . $config['database']['prefix'] . "participantstatus SET pct_signedup=1 WHERE pct_ID = " . $visitor_userid . " AND evt_ID = " . $eventID;
	if (mysql_query($sql)) {
		$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_ID = " . $eventID;
		$result = mysql_query($sql);
		$event = mysql_fetch_array($result);
		$remainingSpaces = $event['evt_remainingPlaces'] - 1;
		$sql = "UPDATE " . $config['database']['prefix'] . "events SET evt_remainingPlaces=" . $remainingSpaces . " WHERE evt_ID = " . $eventID;
		if (mysql_query($sql)) {
			die('<div class="alert alert-success"><h4>Successfully signed up!</h4></div><a href="index.php" class="btn btn-primary">Return</a>');		
		} else {
			die('An error occured. :(<br />Please contact Skull on the BreachLAN forums and let him know that there is an issue. Error Code SignUp-2');
		}
	} else {
		die('An error occured. :(<br />Please contact Skull on the BreachLAN forums and let him know that there is an issue. Error Code SignUp-1');
	}	
}
?>
<h3>Please confirm you would like to sign up to the following event:</h3>
<p>Event Title: <?php echo $event['evt_name'];?></p>
<p>Event Dates: <?php echo date('d', strtotime($event['evt_start_date']));?>-<?php echo date('d F Y', strtotime($event['evt_end_date']));?></p>
<br /><br />
<a href="signup.php?eventid=<?php echo $eventID;?>&confirm=true" class="btn btn-primary">Confirm Sign up</a> <a href="index.php" class="btn">Cancel</a>
<?php require('static/footers.php'); ?>
