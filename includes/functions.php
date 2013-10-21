<?php
function createUserEventInstances($userid, $activeEventID, $config) {
	$conn = mysql_connect($config['database']['host'],$config['database']['user'],$config['database']['pass']);
	mysql_select_db($config['database']['name']);
	$sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_start_date > '" . date('Y-m-d') . "' ORDER BY evt_ID ASC";
	$eventlist = mysql_query($sql);
    $numberEvents = mysql_num_rows($eventlist);

	$sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_id = " . $userid . " AND evt_ID >= " . $activeEventID . " ORDER BY evt_ID ASC";
    $userInstanceList = mysql_query($sql);
    $numberInstances = mysql_num_rows($userInstanceList);
    
    for ($i = 0;$i<$numberEvents;$i++) {
        $eventID = $i + $activeEventID;
        $sql = "SELECT * FROM " . $config['database']['prefix'] . "participantstatus WHERE pct_id = " . $userid . " AND evt_ID = " . $eventID;
        $eventInstance = mysql_query($sql);

        if (mysql_num_rows($eventInstance) == 0) {
            $sql = "INSERT INTO " . $config['database']['prefix'] . "participantstatus (pct_ID, evt_ID, pct_signedup, pct_paid)
            VALUES (
                " . $userid . ",
                " . $eventID . ",
                0,
                0)";
            $insertResult = mysql_query($sql);
        }
    }
}
?>