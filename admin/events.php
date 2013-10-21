<?php require('static/headers.php');?>
<h1>Currently Active Events</h1>
<table class="table table-bordered table-striped tablesorter" style="width: 100%;" id="eventstable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Length (Hours)</th>
            <th>Total Spaces</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
        mysql_connect($config['database']['host'], $config['database']['user'], $config['database']['pass']);
        mysql_select_db($config['database']['name']);
        $sql = "SELECT * FROM " . $config['database']['prefix'] . "events";
        $result = mysql_query($sql);
        
        while ($row = mysql_fetch_array($result)) {?>
        <tr>
            <td><?php echo $row['evt_ID'];?></td>
            <td><?php echo $row['evt_name'];?></td>
            <td><?php echo $row['evt_start_date'];?></td>
            <td><?php echo $row['evt_end_date'];?></td>
            <td><?php echo $row['evt_length'];?></td>
            <td><?php echo $row['evt_size'];?></td>
            <td><a class="btn btn-info btn-small" href="eventinfo.php?eventid=<?php echo $row['evt_ID'];?>">Info</a> <a class="btn btn-primary btn-small" href="addevent.php?mode=edit&eventid=<?php echo $row['evt_ID'];?>">Edit</a> <a class="btn btn-danger btn-small" href="delevent.php?eventid=<?php echo $row['evt_ID'];?>">Delete</a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<div class="row">
    <div class="span3">
        <h2>Add Event</h2>
        <p>Add an event to the active events</p>
        <p><a class="btn btn-primary" href="addevent.php">Add &raquo;</a></p>
    </div>
</div>
<?php require('static/footers.php');?>

<script type="text/javascript">
$(document).ready(function(){
    $("#eventstable").tablesorter();
});
</script>