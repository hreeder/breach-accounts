<?php
require('static/headers.php');
mysql_connect($config['database']['host'], $config['database']['user'], $config['database']['pass']);
mysql_select_db($config['database']['name']);

if (isset($_POST['submit'])) {
	if (isset($_POST['pctPaid'])) {
		$paid = 1;
	} else {
		$paid = 0;
	}
	$sql = "UPDATE " . $config['database']['prefix'] . "participantstatus SET
	pct_paid='" . $paid . "',
	pct_ChosenSeatID='" . $_POST['pctSeat'] . "'
	WHERE pct_instance_ref = " . $_POST['ref'];

	if (!mysql_query($sql)) {
		die('<div class="alert alert-error"><h2>Could not insert data into table</h2></div>' . mysql_error());
	} else {
		die('<h3>Updated Successfully!</h3><a href="eventinfo.php?eventid=' . $_POST['evtID'] . '" class="btn btn-primary">Return</a>');
	}
	
}

$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_instance_ref = " . mysql_real_escape_string($_GET['instanceref']);
$result = mysql_query($sql);
$instance = mysql_fetch_array($result);

$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_id = " . $instance['evt_ID'];
$result = mysql_query($sql);
$event = mysql_fetch_array($result);

XenForo_Autoloader::getInstance()->setupAutoloader($config['xenforo']['path'] . '/library');
XenForo_Session::startPublicSession();
$participant = XenForo_Visitor::setup($instance['pct_ID']);
?>
<h2>Editing Participant</h2>
<h4>Participant:  <?php echo $participant->get('username');?> || Event: <?php echo $event['evt_name'];?></h4>
<br />
<form action="editparticipant.php" method="POST" name="participant" class="form-horizontal">
	<div class="control-group">
		<label class="checkbox">
			<input type="checkbox" name="pctPaid" id="pctPaid" <?php if ($instance['pct_paid'] == 1) {echo "checked=\"yes\"";}?>> Paid
		</label>
	</div>
	<div class="control-group">
		<label for="pctSeat" class="control-label">Selected Seat (If Applicable)</label>
		<div class="controls">
			<input type="text" name="pctSeat" id="pctSeat" <?php if (isset($instance['pct_ChosenSeatID'])) {echo "value=\"" . $instance['pct_ChosenSeatID'] . "\"";}?>>
		</div>
	</div>
	<div class="form-actions">
		<input type="hidden" name="evtID" id="evtID" value="<?php echo $event['evt_ID'];?>">
		<input type="hidden" name="ref" id="ref" value="<?php echo $instance['pct_instance_ref'];?>">
		<input type="submit" class="btn btn-primary" value="Update" name="submit" id="submit" />
		<a href="eventinfo.php?eventid=<?php echo $event['evt_ID'];?>" class="btn">Cancel</a>
	</div>
</form>
<?php require('static/footers.php');?>