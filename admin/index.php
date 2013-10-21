<?php require('static/headers.php');?>
<h1>Welcome, <?php echo $visitor_username;?></h1>
<h4>To the Account Center Administration</h4>
<br />
<div class="row row-fluid">
    <div class="span6">
        <h2>Events</h2>
        <p>Event Administration</p>
        <p><a class="btn btn-primary" href="events.php">Visit &raquo;</a></p>
    </div>
    <div class="span6">
        <h2>Seating Plans</h2>
        <p>Seating Plan Administration</p>
        <p><a class="btn btn-primary" href="seating.php">Visit &raquo;</a></p>
    </div>
</div>
<?php require('static/footers.php');?>