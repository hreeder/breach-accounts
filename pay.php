<?php
require('static/headers.php');
$conn = mysql_connect($config['database']['host'],$config['database']['user'],$config['database']['pass']);
mysql_select_db($config['database']['name']);

$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_instance_ref = " . mysql_real_escape_string($_GET['ref']);
$result = mysql_query($sql);
$instance = mysql_fetch_array($result);

$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_ID = " . $instance['evt_ID'];
$result = mysql_query($sql);
$event = mysql_fetch_array($result);

if ($instance['pct_signedup'] != 1) {
	die('<div class="alert alert-error">You are not signed up for this event yet, please sign up prior to paying.</div><a href="index.php" class="btn btn-primary">Return</a>');
}

if (isset($_GET['confirm']) && $_GET['confirm'] == "true") {
	$sql = "UPDATE " . $config['database']['prefix'] . "participantstatus SET pct_paid=1 WHERE pct_instance_ref = " . mysql_real_escape_string($_GET['ref']);
	if (mysql_query($sql)) {
		die('<div class="alert alert-success"><h4>Successfully paid!</h4></div><a href="index.php" class="btn btn-primary">Return</a>');		
	} else {
		die('<div class="alert alert-error">An error occured. :(<br />Please contact Skull on the BreachLAN forums and let him know that there is an issue. Error Code Pay-1</div>');
	}	
}
?>
<h3>Please confirm you would like to pay for the following event:</h3>
<p>Event Title: <?php echo $event['evt_name'];?></p>
<p>Event Dates: <?php echo date('d', strtotime($event['evt_start_date']));?>-<?php echo date('d F Y', strtotime($event['evt_end_date']));?></p>
<br /><br />
<a href="pay.php?ref=<?php echo $instance['pct_instance_ref'];?>&confirm=true" class="btn btn-primary">Pay</a> <a href="index.php" class="btn">Cancel</a>
<?php require('static/footers.php'); ?>
