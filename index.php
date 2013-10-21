<?php
require('static/headers.php');

$conn = mysql_connect($config['database']['host'],$config['database']['user'],$config['database']['pass']);
mysql_select_db($config['database']['name']);
$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_start_date > '" . date('Y-m-d') . "' ORDER BY evt_ID ASC";
$eventlist = mysql_query($sql);
if (mysql_num_rows($eventlist) == 0) {
	die("No events are currently active. Please try again later.");
}
$activeEvent = mysql_fetch_array($eventlist);

$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_id = " . $visitor_userid . " AND evt_ID >= " . $activeEvent['evt_ID'] . " ORDER BY evt_ID ASC";
$visitor_status = mysql_query($sql);
$activeEventVisitorStatus = mysql_fetch_array($visitor_status);

if (mysql_num_rows($eventlist) != mysql_num_rows($visitor_status)) {
	createUserEventInstances($visitor_userid, $activeEvent['evt_ID'], $config);
	$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_start_date > '" . date('Y-m-d') . "' ORDER BY evt_ID ASC";
	$eventlist = mysql_query($sql);
	$activeEvent = mysql_fetch_array($eventlist);

	$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_id = " . $visitor_userid . " AND evt_ID >= " . $activeEvent['evt_ID'] . " ORDER BY evt_ID ASC";
	$visitor_status = mysql_query($sql);
	$activeEventVisitorStatus = mysql_fetch_array($visitor_status);
}

$userStatus = "null";

if ($activeEventVisitorStatus['pct_signedup'] == 1) {
	if ($activeEventVisitorStatus['pct_paid'] == 1) {
		if ($activeEventVisitorStatus['pct_ChosenSeatID']) {
			$userStatus = "chosenseat";
		} else {
			$userStatus = "paid";
		}
	} else {
		$userStatus = "signedup";
	}
}
?>
<h1>Welcome, <?php echo $visitor_username;?></h1>
<h4>To the Account Center</h4>
<br />
<div class="next-event">
	<h3><?php echo $activeEvent['evt_name'];?></h3>
	<h4><?php echo date('d', strtotime($activeEvent['evt_start_date']));?>-<?php echo date('d F Y', strtotime($activeEvent['evt_end_date']));?> :: Status</h4>
	<h4>Total Places: <?php echo $activeEvent['evt_size'];?> || Remaining Spaces: <?php echo $activeEvent['evt_remainingPlaces'];?></h4>
	<div id="progressbar-area">
		<div class="progress">
			<div class="bar<?php if ($userStatus == "signedup" || $userStatus == "paid" || $userStatus == "chosenseat") {echo " bar-success";} ?>" style="width: 33%;">Signed Up</div>
			<div class="bar<?php if ($userStatus == "chosenseat" || $userStatus == "paid") {echo " bar-success";} elseif ($userStatus == "null") {echo " bar-danger";} ?>" style="width: 33%;">Paid</div>
			<div class="bar<?php if ($userStatus == "chosenseat") {echo " bar-success";} elseif ($userStatus == "null" || $userStatus == "signedup") {echo " bar-danger";} ?>" style="width: 34%;">Seat Selected</div>
		</div>
	</div>
</div>

<hr>

<div class="upcoming-events">
	<h3>Upcoming Events</h3>
	<table class="table table-bordered table-striped" style="width: 100%;" id="eventstable">
		<thead>
			<tr>
				<th>Event Title</th>
				<th>Signed Up</th>
				<th>Paid</th>
				<th>Seat</th>
				<th>Length</th>
				<th>Dates</th>
				<th>Spaces Left</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
            <tr class="info">
				<td><?php echo $activeEvent['evt_name']; ?></td>
				<td><?php if ($activeEventVisitorStatus['pct_signedup'] == 1) {echo "Yes";} else {echo "No";}?></td>
				<td><?php if ($activeEventVisitorStatus['pct_paid'] == 1) {echo "Yes";} else {echo "No";}?></td>
				<td><?php if ($activeEventVisitorStatus['pct_ChosenSeatID']) {
					echo $activeEventVisitorStatus['pct_ChosenSeatID'];
				} else {
					echo "None Chosen";
				}?></td>
				<td><?php echo $activeEvent['evt_length']; ?> Hour</td>
				<td><?php echo date('d', strtotime($activeEvent['evt_start_date']));?>-<?php echo date('d F Y', strtotime($activeEvent['evt_end_date']));?></td>
				<td><?php echo $activeEvent['evt_remainingPlaces'];?></td>
				<td>
					<div class="btn-toolbar">
						<div class="btn-group">
							<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
								Actions
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<div class="btn-group">
									<a class="btn<?php if ($userStatus == "signedup" || $userStatus == "paid" || $userStatus == "chosenseat") {echo " disabled";} else {echo " btn-primary";} ?>" href="<?php if ($userStatus == "null") {echo 'signup.php?eventid=' . $activeEvent['evt_ID'];} else {echo '#';}?>">Sign Up</a>
									<a class="btn<?php if ($userStatus == "chosenseat" || $userStatus == "paid") {echo " disabled";} elseif ($userStatus == "signedup") {echo " btn-primary";} ?>" href="<?php if ($userStatus == "signedup") {echo 'pay.php?ref=' . $activeEventVisitorStatus['pct_instance_ref'];} else {echo '#';}?>">Pay</a>
									<a class="btn<?php if ($userStatus == "chosenseat") {echo " disabled";} elseif ($userStatus == "paid") {echo " btn-primary";} ?>" href="#">Select Seat</a>
								</div>
								<a href="eventinfo.php?eventid=<?php echo $activeEvent['evt_ID'];?>" class="btn btn-info">Info</a>
							</ul>
						</div>
					</div>
				</td>
			</tr>
            <?php
                while ($row = mysql_fetch_array($eventlist)) {
                	$activeEventVisitorStatus = mysql_fetch_array($visitor_status);
                	$userStatus = "null";
					if ($activeEventVisitorStatus['pct_signedup'] == 1) {
						if ($activeEventVisitorStatus['pct_paid'] == 1) {
							if ($activeEventVisitorStatus['pct_ChosenSeatID']) {
								$userStatus = "chosenseat";
							} else {
								$userStatus = "paid";
							}
						} else {
							$userStatus = "signedup";
						}
					}
                ?>
			<tr>
				<td><?php echo $row['evt_name']; ?></td>
				<td><?php if ($activeEventVisitorStatus['pct_signedup'] == 1) {echo "Yes";} else {echo "No";}?></td>
				<td><?php if ($activeEventVisitorStatus['pct_paid'] == 1) {echo "Yes";} else {echo "No";}?></td>
				<td><?php if ($activeEventVisitorStatus['pct_ChosenSeatID']) {
					echo $activeEventVisitorStatus['pct_ChosenSeatID'];
				} else {
					echo "None Chosen";
				}?></td>
				<td><?php echo $row['evt_length']; ?> Hour</td>
				<td><?php echo date('d', strtotime($row['evt_start_date']));?>-<?php echo date('d F Y', strtotime($row['evt_end_date']));?></td>
				<td><?php echo $row['evt_remainingPlaces'];?></td>
				<td>
					<div class="btn-toolbar">
						<div class="btn-group">
							<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
								Actions
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<div class="btn-group">
									<a class="btn<?php if ($userStatus == "signedup" || $userStatus == "paid" || $userStatus == "chosenseat") {echo " disabled";} else {echo " btn-primary";} ?>" href="<?php if ($userStatus == "null") {echo 'signup.php?eventid=' . $row['evt_ID'];} else {echo '#';}?>">Sign Up</a>
									<a class="btn<?php if ($userStatus == "chosenseat" || $userStatus == "paid") {echo " disabled";} elseif ($userStatus == "signedup") {echo " btn-primary";} ?>" href="<?php if ($userStatus == "signedup") {echo 'pay.php?ref=' . $activeEventVisitorStatus['pct_instance_ref'];} else {echo '#';}?>">Pay</a>
									<a class="btn<?php if ($userStatus == "chosenseat") {echo " disabled";} elseif ($userStatus == "paid") {echo " btn-primary";} ?>" href="#">Select Seat</a>
								</div>
								<a href="eventinfo.php?eventid=<?php echo $row['evt_ID'];?>" class="btn btn-info">Info</a>
							</ul>
						</div>
					</div>
				</td>
			</tr>
            <?php
        	} ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#eventstable").tablesorter();
});
</script>
<?php require('static/footers.php'); ?>