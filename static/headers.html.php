<!DOCTYPE html>
<html>
<head>
<title>BreachLAN Account</title>
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/breachlan.css" type="text/css">
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>
<script src="js/jquery.tablesorter.min.js" type="text/javascript"></script>
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
                    <?php
                        if ($admin) {
                            echo "<ul class=\"nav\">";
                            echo "<li><a href=\"./admin/\">Administration</a></li>";
                            echo "</ul>";
                        }
                    ?>
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