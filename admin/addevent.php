<?php
require('static/headers.php');

$conn = mysql_connect($config['database']['host'], $config['database']['user'], $config['database']['pass']) or die(mysql_error());
mysql_select_db($config['database']['name']) or die(mysql_error());

if (isset($_GET['mode'])) {
    if ($_GET['mode'] == 'edit' && isset($_GET['eventid'])) {
        $sql = "SELECT * FROM " . $config['database']['prefix'] . "events WHERE evt_ID = " . mysql_real_escape_string($_GET['eventid']);
        $result = mysql_query($sql);
        $event = mysql_fetch_array($result);
    } else if ($_GET['mode'] == 'update' && isset($_POST['submit'])) {
        $sql = "UPDATE " . $config['database']['prefix'] . "events SET
        evt_name='" . $_POST['eventName'] . "', 
        evt_start_date='" . $_POST['eventStartDate'] . "', 
        evt_end_date='" . $_POST['eventEndDate'] . "',
        evt_length='" . $_POST['eventLength'] . "',
        evt_size='" . $_POST['eventSize'] . "'
        WHERE evt_ID = " . $_POST['eventid'];
        if (!mysql_query($sql)) {
           die('<div class="alert alert-error"><h2>Could not insert data into table</h2></div>' . mysql_error());
        } else {
           die('<h3>Event Updated!</h3><a href="events.php" class="btn btn-primary">Return</a>');
        }
    }
}

if (isset($_POST['submit']) && !isset($_GET['mode'])) {
    $sql = "INSERT INTO " . $config['database']['prefix'] . "events (evt_name, evt_start_date, evt_end_date, evt_length, evt_size, evt_remainingPlaces)
    VALUES (
        '" . $_POST['eventName'] . "',
        '" . $_POST['eventStartDate'] . "',
        '" . $_POST['eventEndDate'] . "',
        " . $_POST['eventLength'] . ",
        " . $_POST['eventSize'] . ",
        " . $_POST['eventSize'] . "
    )";
    
    if (!mysql_query($sql)) {
        die('<div class="alert alert-error"><h2>Could not insert data into table</h2></div>' . mysql_error());
    } else {
        die('<h3>Event Added!</h3><a href="events.php" class="btn btn-primary">Return</a>');
    }
}
?>

<h2>New Event</h2>
<form class="form-horizontal" name="event" method="POST" action="addevent.php<?php if (isset($event)){echo "?mode=update";}?>">
    <div class="control-group">
        <label class="control-label" for="eventName">Name</label>
        <div class="controls">
            <input name="eventName" type="text" id="eventName" placeholder="BreachLAN XX" <?php if (isset($event)) {echo "value=\"" . $event['evt_name'] . "\"";}?>>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="eventStartDate">Start Date</label>
        <div class="controls">
            <input name="eventStartDate" type="text" id="eventStartDate" placeholder="YYYY-MM-DD" <?php if (isset($event)) {echo "value=\"" . $event['evt_start_date'] . "\"";}?>>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="eventEndDate">End Date</label>
        <div class="controls">
            <input name="eventEndDate" type="text" id="eventEndDate" placeholder="YYYY-MM-DD" <?php if (isset($event)) {echo "value=\"" . $event['evt_end_date'] . "\"";}?>>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="eventLength">Length in Hours</label>
        <div class="controls">
            <input name="eventLength" type="text" id="eventLength" placeholder="72" <?php if (isset($event)) {echo "value=\"" . $event['evt_length'] . "\"";}?>>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="eventSize">Spaces</label>
        <div class="controls">
            <input name="eventSize" type="text" id="eventSize" placeholder="50" <?php if (isset($event)) {echo "value=\"" . $event['evt_size'] . "\"";}?>>
        </div>
    </div>
        
    <div class="form-actions">
        <?php 
        if (isset($event)) {
            echo "<input type=\"hidden\" id=\"eventid\" name=\"eventid\" value=\"" . $event['evt_ID'] . "\">";
        }
        ?>
        <input class="btn btn-primary" type="submit" value="<?php if(isset($event)) {echo "Save Event";} else {echo "Add Event";}?>" id="submit" name="submit" />
        <a href="events.php" class="btn">Cancel</a>
    </div>
</form>

<?php require('static/footers.php');?>