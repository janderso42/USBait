<?php
session_start();
#Author : Joshua Anderson
#name   : manage.php
#purpose: allows admin to view and manage all users and all campaigns
#date   : 2017/3/9
#version: 1.0

include_once('/var/www/html/project-lib.php');
include_once('/var/www/html/header.php');

#logoutCheck();   ##if logout has been pressed and we have an active session, remove it and redirect to index page

checkAdmin();

connect($db);
icheck($s);
icheck($cid);


switch($s) {
	case 10;
	default: ##List Users
		echo "
		<div class='container'>
  		  <h2>User Management</h2>
  	 	  <table class='table table-striped'>
    		    <thead>
     		      <tr>
        	       <th>Users</th>
      		      </tr>
    		    </thead>
    		    <tbody>";

                $query="SELECT username, userid FROM users";
                $result=mysqli_query($db, $query);
                while($row=mysqli_fetch_row($result)) {
			echo "<tr><td> $row[0] </td><td>
			<form><input type=hidden name=s value=13> 
			<input type=hidden name=addUser value=$row[0]>
			<input type=hidden name=uid value=$row[1]> 
			<input type=submit class='btn btn-default' name='resetPass' value='Reset Password'> 
			</td></tr> </form><br><br>";
                }
                echo"</tbody></table></div>";
	break;
	case 11;        ##Add new user
			##Get new user's name and password
                echo "
		<div class='container'>
  		  <h2>User Management</h2>
  	 	  <table class='table'>
    		    <thead>
     		      <tr>
        	       <th>Add New User</th>
      		      </tr>
    		    </thead>
    		    <tbody>
                <form method=post action=manage.php> 
                        <tr> <td> Username </td> <td> <input type=text name=addUser value=''> </td> </tr>
			<tr> <td> Password </td> <td> <input type=text name=addPass value=''> </td> </tr>
			<tr><td><select name=addStatus>
			  <option value='Client'>Client</option>
			  <option value='Engineer'>Engineer</option>
			  <option value='Admin'>Admin</option>
			</select></td>
			<td colspan=2> <input type=hidden name=s value=12> 
			<input type=submit name=submit class='btn btn-primary' value=submit> </td></tr>
                </tbody></table></div>
                        </form><br> <br>";
        break;
        case 12;        ##add new user and their password after salting
                $addUser=mysqli_real_escape_string($db,$addUser);
                $addPass=mysqli_real_escape_string($db,$addPass);
                $addStatus=mysqli_real_escape_string($db,$addStatus);

                if($addUser==null || $addPass==null){
                        echo "Error: Neither username nor password can be null";
                        exit;
                }

                $rand=mt_rand().$addUser.date("h:i:s");         #Salt is generated with a random number cat w/ the user's name and the time
                $salt=hash('sha256',$rand);
                $newPass=hash('sha256',$addPass.$salt);
                if($stmt=mysqli_prepare($db, "INSERT INTO users SET userid='', username=?, password=?, salt=?, status=?")){
                        mysqli_stmt_bind_param($stmt, "ssss", $addUser, $newPass, $salt, $addStatus);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                } else {
                        echo "Error with database query";
                        exit;
                }
                echo "$addUser successfully added to users";
		header( "refresh:3;url=manage.php?s=10" );


        break;
	case 13;	##Reset Password for an existing user 
			##Get new password to update for given user. Stepped to from list of users displayed on user management page. passes in s=92, the uid and username
                $uid=mysqli_real_escape_string($db,$uid);
                $addUser=mysqli_real_escape_string($db,$addUser);       #I reused the addUser variable, even tho we aren't fully adding a new user. Just updating their password and salt.
                echo "
                <form method=post action=manage.php> 
                        <table> <tr> <td colspan=2> Updating info for $addUser </td> </tr>
                        <tr> <td> New Password </td> <td> <input type=text name=addPass value=''> </td> </tr>
                        <tr> <td colspan=2> <input type=hidden name=s value=14><input type=hidden name=addUser value=$addUser> <input type=hidden name=uid value=$uid><input type=submit name=submit value=submit> </td></tr>
                        </table> 
                        </form><br> <br>";

	break;
	case 14; ##Salt and update new password and salt to db
                $uid=mysqli_real_escape_string($db,$uid);
                $addPass=mysqli_real_escape_string($db,$addPass);
                $addUser=mysqli_real_escape_string($db,$addUser);
                if($addUser==null || $addPass==null){
                        echo "Error: Neither username nor password can be null";
                        exit;
                }

                $rand=mt_rand().$addUser.date("h:i:s");
                $salt=hash('sha256',$rand);
                $newPass=hash('sha256',$addPass.$salt);
#               echo "<script type='text/javascript'>alert('$uid $newPass $salt');</script>";

                if($stmt=mysqli_prepare($db, "UPDATE users SET password=?, salt=? WHERE userid=?")){
                        mysqli_stmt_bind_param($stmt, "ssi", $newPass, $salt, $uid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                } else {
                        echo "Error with database query, password not updated";
                        exit;
                }
                echo "$addUser's password has been sucesfully updated";
		header( "refresh:3;url=manage.php?s=10" );
	break;
	case 20;	##View campaigns
		echo "
		<div class='container'>
		  <h2>Campaign Manager</h2>
		  <table class='table table-striped'>
		    <thead>
		      <tr>
		        <th>Location</th>
		      </tr>
		    </thead>
		    <tbody>";

                $query="SELECT client, location, campaignid FROM campaigns";
                $result=mysqli_query($db, $query);
                while($row=mysqli_fetch_row($result)) {
                        echo "<tr><td> $row[1] </td>
                        <td> 
                        <form method=post action=details.php>
                                <input type=hidden name=s value=0>
                                <input type=hidden name=cid value=$row[2]>
                                <input type=hidden name=client value=$row[0]>
                                <input type=hidden name=location value=$row[1]>
                                <input type=submit class='btn btn-default' name='details' value='Details'>
                        </td></tr> </form><br><br>";
                }
                echo"</tbody></table></div>";

	break;
	case 21; 	##Create Campaign
	##Get Campaign Info
	##Add dropdowns for team and client
		$query="SELECT username FROM users WHERE status='Client'";
		$result=mysqli_query($db, $query);

                echo "
                <div class='container'>
                  <h2>Manage Campaigns</h2>
                  <table class='table'>
                    <thead>
                      <tr>
                       <th>Create New Campaign</th>
                      </tr>
                    </thead>
                    <tbody>
                <form method=post action=manage.php> 
                        <tr> <td> Location </td> <td> <input type=text name=location value=''> </td> </tr>
			<tr><td><label>Client   </label></td><td>
			<select name='Client'>";
			while($row=mysqli_fetch_row($result)) {
				echo "<option value=$row[0]>$row[0]</option>";
			}
			echo"
                        </select></td>
                        <td colspan=2> <input type=hidden name=s value=22> 
                        <input type=submit name=submit class='btn btn-primary' value=submit> </td></tr>
                </tbody></table></div>
                        </form><br> <br>";

        break;
        case 22;        ##add new campaign, their location, and create token
                $addClient=mysqli_real_escape_string($db,$Client);
                $addLocation=mysqli_real_escape_string($db,$location);

                if($addLocation==null || $addClient==null){
                        echo "Error: Neither username nor password can be null";
                        echo "Location: $addLocation  Client: $addClient";
                        exit;
                }
                if ($stmt=mysqli_prepare($db, "SELECT userid FROM users WHERE username =? AND status='Client'")){
                        mysqli_stmt_bind_param($stmt, "s", $addClient);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $uid);
                        while(mysqli_stmt_fetch($stmt)){
                                $bid=htmlspecialchars($uid);
                        }
                        mysqli_stmt_close($stmt);
		} else {
                        echo "Error with database query 1";
                        exit;
		}
	
		$rand=mt_rand().$addClient.date("h:i:s");
                $token=hash('sha256',$rand);
                if($stmt=mysqli_prepare($db, "INSERT INTO campaigns SET campaignid='', usbcount=0, client=?, location=?, ctoken=?")){
                        mysqli_stmt_bind_param($stmt, "iss", $uid, $addLocation, $token);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                } else {
                        echo "Error with database query 2";
                        exit;
                }
		echo "$addUser successfully created Campaign";
		header( "refresh:3;url=manage.php?s=20" );
		
}
include_once('footer.php');
?>


