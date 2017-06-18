<?php
session_start();
#Author : Joshua Anderson
#name   : index.php
#purpose: Landing page for users 
#date   : 2017/3/13
#version: 1.0
include_once('/var/www/html/project-lib.php');
include_once('/var/www/html/header.php');
connect($db);
icheck($s);
icheck($cid);

if($postLog!=null){				#If user has pressed logout, check for session and destory it
	if(isset($_SESSION['authenticated'])){
                session_destroy();
       		 echo "<script type='text/javascript'>alert('You have been logged out');</script>";
                header("refresh:.5;url=/login.php");

	}
}

if(!isset($_SESSION['authenticated'])){		#If session is not set, redirect to login
	authenticate($db, $postUser, $postPass);
}

echo "
	<div class='container'>
	  <h2>Your Campaigns</h2>
	  <table class='table table-striped'>
	    <thead>
	      <tr>
	        <th>Location</th>
	      </tr>
	    </thead>
	    <tbody>";

$user=$_SESSION['userid'];
$query="SELECT DISTINCT c.campaignid, c.client, c.location FROM campaigns c, teams t, users u WHERE t.uid=$user AND t.cid=c.campaignid";
$result=mysqli_query($db, $query);
while($row=mysqli_fetch_row($result)) {
	echo "<tr><td> $row[2] </td>
		<td><form method=post action=details.php>
		<input type=hidden name=s value=0> 
        	<input type=hidden name=cid value=$row[0]>
        	<input type=hidden name=client value=$row[1]> 
        	<input type=hidden name=location value=$row[2]> 
        	<input type=submit class='btn btn-default' name='details' value='Details'> 
        </td></tr> </form><br><br>";
}
echo"</tbody>
     </table>
</div>";

include_once('footer.php');
?>

