<?php
require('static/headers.php');
if (!isset($_GET['eventid'])) {
	die("No event ID. Please specify an event");
}

mysql_connect($config['database']['host'], $config['database']['user'], $config['database']['pass']);
mysql_select_db($config['database']['name']);
$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_id = " . mysql_real_escape_string($_GET['eventid']);
$result = mysql_query($sql);
$event = mysql_fetch_array($result);

$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE evt_id = " . mysql_real_escape_string($_GET['eventid']) . " AND pct_signedup = 1 ORDER BY pct_paid DESC, pct_instance_ref ASC";
$participants = mysql_query($sql);
?>
<h2>Event: <?php echo $event['evt_name'];?></h2>
<p>Start Date: <?php echo $event['evt_start_date'];?> || End Date: <?php echo $event['evt_end_date'];?> || Length: <?php echo $event['evt_length'];?> Hours</p>
<p><?php $bookedSpaces = $event['evt_size'] - $event['evt_remainingPlaces'];
echo $bookedSpaces;?> Spaces Booked || <?php echo $event['evt_remainingPlaces'];?> Spaces Free || <?php echo $event['evt_size'];?> Spaces Total</p>
<h4>Participants</h4>
<table class="table">
	<thead>
		<tr>
			<th>#</th>
			<th>Username</th>
			<th>Status</th>
			<th>Seat</th>
			<th>Payment Method</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		XenForo_Autoloader::getInstance()->setupAutoloader($config['xenforo']['path'] . '/library');
		XenForo_Session::startPublicSession();

		while($participant = mysql_fetch_array($participants)) {
			$person = XenForo_Visitor::setup($participant['pct_ID']);
		?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $person->get('username');?></td>
			<td><?php
				if ($participant['pct_signedup'] == 1) {
					if ($participant['pct_paid'] == 1) {
						echo "Paid";
					} else {
						echo "Signed Up";
					}
				}
			?></td>
			<td><?php if ($participant['pct_ChosenSeatID']) {
							echo $participant['pct_ChosenSeatID'];
						} else {
							echo "None Chosen Yet";
						}?></td>
			<td></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							Actions
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">&nbsp;
							<a class="btn btn-primary" href="editparticipant.php?instanceref=<?php echo $participant['pct_instance_ref'];?>">Edit</a>
							<a class="btn btn-danger" href="removeparticipant.php?instanceref=<?php echo $participant['pct_instance_ref'];?>">Remove</a>
						&nbsp;</ul>
					</div>
				</div>
			</td>
		</tr>
		<?php $i++;}?>
	</tbody>
</table>
<div class="btn-toolbar">
	<div class="btn-group">
		<a href="events.php" class="btn">Return to Event List</a>
	</div>
</div>
<?php require('static/footers.php');?>