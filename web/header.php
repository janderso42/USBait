<?php
#name: Josh Anderson
#purpose: header for usb tracker
#date: 2017/4/6
#version: 1.0
echo "
<!DOCTYPE html>
<html lang='en'>
    <head>
	<title>USBait</title>
	 <meta charset='utf-8'>		
	 <meta name='viewport' content='width=device-width, initial-scale=1'>
	 <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
	 <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
	 <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
	 <style>
	   /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
	   .row.content {height: 550px}
	   
	   /* Set gray background color and 100% height */
	   .sidenav {
	     background-color: #f1f1f1;
	     height: 100%;
	   }
	       
	   /* On small screens, set height to 'auto' for the grid */
	   @media screen and (max-width: 767px) {
	     .row.content {height: auto;} 
	   }
	 </style>
    </head> 
    <body>

<nav class='navbar navbar-inverse'>
  <div class='container-fluid'>
    <div class='navbar-header'>
      <a class='navbar-brand' href='/index.php'>USBait</a>
    </div>";
if(isset($_SESSION['authenticated'])){                  ##If User is logged in, display user info
    $uname=$_SESSION['usrname'];
    echo"
    <ul class='nav navbar-nav'>
      <li class='active'><a href='/index.php'>Home</a></li>";
    if($_SESSION['status']=='Admin'){
      echo"
      <li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='/manage.php?s=10'>Manage Users <span class='caret'></span></a>
	<ul class='dropdown-menu'>
	  <li><a href='/manage.php?s=10'>View Users</a></li>
	  <li><a href='/manage.php?s=11'>Add User</a></li>
	</ul>
      </li>
      <li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='/manage.php?s=20'>Manage Campaigns <span class='caret'></span></a>
	<ul class='dropdown-menu'>
	  <li><a href='/manage.php?s=20'>View Campaigns</a></li>
	  <li><a href='/manage.php?s=21'>Add Campaigns</a></li>
	</ul>
      </li>";

    }
    echo"</ul>
    <ul class='nav navbar-nav navbar-right'>
      <li><a href=''><span class='glyphicon glyphicon-user'></span> Logged in as $uname</a></li>
      <li><a href='/index.php?postLog=1'><span class='glyphicon glyphicon-log-in'></span> Logout</a></li>
    </ul>	

";
}
echo"
</div>
</nav>
  
 \n<hr>";
?>


