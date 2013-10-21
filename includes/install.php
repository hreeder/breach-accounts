<?php
require_once('config.php');
?>
<html>
<head>
<title>BreachLAN Account || Installer</title>
<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="../css/breachlan.css" type="text/css" />
<script src="../js/jquery.min.js" type="text/javascript"></script>
<script src="../js/bootstrap.js" type="text/javascript"></script>
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="http://www.breachlan.co.uk/"></a>
				<ul class="nav">
					<li><a href="http://www.breachlan.co.uk/account/">Account Home</a></li>
					<li><a href="#">Events</a></li>
				</ul>
				<ul class="nav pull-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Return <b class="caret"></b></a>
						<ul class="dropdown-menu">
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
    <h1>BreachLAN Account Center</h1>
    <h2>Install Script</h2>
<?php
/*
*   BEGIN INSTALL SCRIPT HERE
*   ALL GOOD TO ECHO STATEMENTS FROM HERE ON
*/

echo('<h4>Beginning Pre-Installation Sanity Checks</h4>');
if (!isset($config['database']['host'])) {
    echo('<div class="alert alert-error">Hostname not Set</div>');
    die();
} else {
    echo('Hostname Set');
}
if (!isset($config['database']['name'])) {
    echo('<br /><div class="alert alert-error">Database Name not Set</div>');
    die();
} else {
    echo('<br />Databse Name Set');
}
if (!isset($config['database']['user'])) {
    echo('<br /><div class="alert alert-error">No Username Set</div>');
    die();
} else {
    echo('<br />Username Set');
}
if (!isset($config['database']['pass'])) {
    echo('<br /><div class="alert alert-error">No Password Set</div>');
    die();
} else {
    echo('<br />Password Set');
}
?>
<br />
<h4>Pre-Installation Sanity Checks Completed Successfully. Continuing...</h4>
<h4>Next Up: Database Table Creation</h4>
<?php
$conn = mysql_connect($config['database']['host'], $config['database']['user'], $config['database']['pass']);
if (!$conn) {
    die('<div class="alert alert-error"><h2>Could not connect to database server. Aborting!</h2>
    Attempting to use ' . $config['database']['host'] . ' as host, '
    . $config['database']['user'] . ' as username and '
    . $config['database']['pass'] . ' as password.</div>');
} else {
    echo ('Connected to database server successfully!');
}
$selected_database = mysql_select_db($config['database']['name']);
if (!$selected_database) {
    die('<div class="alert alert-error"><h2>Could not select database, does it exist?!</h2></div>');
} else {
    echo ('<br />Selected database successfully!<br />');
}

echo('<br />Attempting to create Events table');

$sql = "CREATE TABLE " . $config['database']['prefix'] . "events (
evt_ID int NOT NULL AUTO_INCREMENT,
PRIMARY KEY(evt_ID),
evt_name varchar(15),
evt_start_date DATE,
evt_end_date DATE,
evt_size int,
evt_remainingPlaces int,
evt_length int) ENGINE=InnoDB";

$query = mysql_query($sql);

if (!$query) {
    die('<div class="alert alert-error"><h2>Could not create events table</h2></div>');
} else {
    echo('<br />Success: Event Table Created!<br />');
}

echo('<br />Attempting to create ParticipantStatus table');

$sql = "CREATE TABLE " . $config['database']['prefix'] . "participantstatus (
pct_instance_ref int NOT NULL AUTO_INCREMENT,
PRIMARY KEY(pct_instance_ref),
pct_ID int NOT NULL,
evt_ID int NOT NULL,
pct_signedup BOOLEAN NOT NULL,
pct_paid BOOLEAN NOT NULL,
pct_ChosenSeatID int) ENGINE=InnoDB";

$query = mysql_query($sql);

if (!$query) {
    die('<div class="alert alert-error"><h2>Could not create ParticipantStatus table</h2></div>');
} else {
    echo('<br />Success: ParticipantStatus Table Created!');
}

$sql = "ALTER TABLE " . $config['database']['prefix'] . "participantstatus
ADD CONSTRAINT FK_instanceToEvent
FOREIGN KEY (evt_ID) REFERENCES " . $config['database']['prefix'] . "events(evt_ID)
ON UPDATE CASCADE
ON DELETE CASCADE";

$query = mysql_query($sql);

if (!$query) {
    die('<div class="alert alert-error"><h2>Unable to add Foreign Keys to ParticipantStatus table.</h2></div>');
} else {
    echo('<br />Success: ParticipantStatus Foreign Keys added!<br />');
}

// We're going to create a table to hold instances of seatingplans for each event. This only needs to have a seatID
echo('<br />Attempting to create EventSeating table');

$sql = "CREATE TABLE " . $config['database']['prefix'] . "eventseating (
seat_ID int NOT NULL AUTO_INCREMENT,
PRIMARY KEY(seat_ID),
evt_id int NOT NULL,
seat_status ENUM('FREE', 'RESERVED', 'STAFF', 'LOCKED'),
seat_templateID int NOT NULL,
set_templateSeatID int NOT NULL,
seat_name varchar(8),
seat_bookedinstance int) ENGINE=InnoDB";

$query = mysql_query($sql);

if (!$query) {
    die('<div class="alert alert-error"><h2>Could not create EventSeating table</h2></div>');
} else {
    echo('<br />Success: EventSeating Table Created!<br />');
}

$sql = "ALTER TABLE " . $config['database']['prefix'] . "eventseating
ADD CONSTRAINT FK_seatingToEvent
FOREIGN KEY (evt_ID) REFERENCES " . $config['database']['prefix'] . "events(evt_ID)
ON UPDATE CASCADE
ON DELETE CASCADE";

// We now want to create the table for seating templates (where a user can define a new seating plan)
echo('<br />Attempting to create seatingTemplate table');


$query = mysql_query($sql);
if (!$query) {
    die('<div class="alert alert-error"><h2>Unable to add Foreign Keys to ParticipantStatus table.</h2>evt_id reference.</div>');
} else {
    echo('<br />Success: ParticipantStatus Foreign Keys added!<br />');
}

?>
    <div class="alert"><h2>Install Complete</h2></div>
 </div>
</div>
<div class="container">
	<footer>
		Copyright © BreachLAN 2012 - 2012. All Rights Reserved.
	</footer>
</div>
</body>
</html>