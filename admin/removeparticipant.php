<?php
require('static/headers.php');
mysql_connect($config['database']['host'], $config['database']['user'], $config['database']['pass']);
mysql_select_db($config['database']['name']);
$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_instance_ref = " . mysql_real_escape_string($_GET['instanceref']);
$result = mysql_query($sql);
$instance = mysql_fetch_array($result);

$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_id = " . $instance['evt_ID'];
$result = mysql_query($sql);
$event = mysql_fetch_array($result);

XenForo_Autoloader::getInstance()->setupAutoloader($config['xenforo']['path'] . '/library');
XenForo_Session::startPublicSession();
$participant = XenForo_Visitor::setup($instance['pct_ID']);

if (isset($_GET['confirm'])) {
	if ($_GET['confirm'] == "true") {
		$sql = "DELETE FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_instance_ref = " . mysql_real_escape_string($_GET['instanceref']);
		if (!mysql_query($sql)) {
		die('<div class="alert alert-error"><h2>Could not remove user</h2></div>' . mysql_error());
		} else {
			$remainingplaces = $event['evt_remainingPlaces'] + 1;
			$sql = "UPDATE " . $config['database']['prefix'] . "events SET evt_remainingPlaces = '" . $remainingplaces . "' WHERE evt_ID = " . $event['evt_ID'];
			if (!mysql_query($sql)) {
				die('<div class="alert alert-error"><h2>Could not update the event\'s remaining places. User has still been removed. Please contact Skull with this error.</h2></div>' . mysql_error());
			} else {
				die('<h3>User Removed!</h3><a href="eventinfo.php?eventid=' . $instance['evt_ID'] . '" class="btn btn-primary">Return</a>');
			}
			
		}
	}
}
?>
<h2>Remove Participant: Are You Sure?</h2>
<h4>You are about to remove <?php echo $participant->get('username');?> from <?php echo $event['evt_name'];?>. The system will not remember the user's payment if this process is completed and the user re-signs up.</h4>
<br />
<a href="removeparticipant.php?instanceref=<?php echo $_GET['instanceref'];?>&confirm=true" class="btn btn-danger">Confirm</a>
<a href="events.php" class="btn">Cancel</a>
<?php require('static/footers.php');?>