<?php
session_start();
#Author : Joshua Anderson
#name   : details.php
#purpose: Queries and displays detailed data for a specific campaign. Clients can view, team members can enter data. 
#date   : 2017/3/9
#version: 1.0

include_once('/var/www/html/project-lib.php');
include_once('/var/www/html/header.php');

#logoutCheck();   ##if logout has been pressed and we have an active session, remove it and redirect to index page

connect($db);
icheck($s);
icheck($cid);

$stat=campaignPermissions($db, $cid);   		##Ensure all viewers have permission to see this campaign
	
if($stat !== 'client' AND $stat !== 'team' AND $stat !== 'lead' AND $_SESSION['status'] !=='Admin'){  		
	      echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      echo "Unauthorized Access";
	      header("refresh:1;url=/index.php");
	      exit;
}


switch($s) {
	default:
	case 0;
		$cid=mysqli_real_escape_string($db,$cid);
		$client=htmlspecialchars($client);
		$location=htmlspecialchars($location);
		if($stat == 'team' OR $stat == 'lead' OR $_SESSION['status']='Admin') {
                        echo "<td><tr><form method=post action=details.php>
                                <input type=hidden name=s value=1>
                                <input type=hidden name=cid value=$cid>
                                <input type=hidden name=client value=$client>
                                <input type=hidden name=location value=$location>
                                <input type=submit name='viewTeam' value='View Team'>
                              </td></form>
                              <td><form method=post action=details.php>
                                <input type=hidden name=s value=5>
                                <input type=hidden name=cid value=$cid>
                                <input type=hidden name=client value=$client>
                                <input type=hidden name=location value=$location>
                                <input type=submit name='data' value='Enter Data'>
                              </td></tr></form><br><br>";
		}
		echo "<table><tr><td> <b> <u> $location Campaign Details </b></u></td></tr> \n";
                if ($stmt=mysqli_prepare($db, "SELECT u.username, c.usbcount, c.start, c.end FROM campaigns c, users u WHERE c.campaignid = ? AND c.client=u.userid")){
                        mysqli_stmt_bind_param($stmt, "i", $cid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $username, $usbCount, $start, $end);
                        while(mysqli_stmt_fetch($stmt)){
                                $username=htmlspecialchars($username);
                                $usbCount=htmlspecialchars($usbCount);
                                $start=htmlspecialchars($start);
                                $end=htmlspecialchars($end);
				echo "<tr><td>Client: </td><td> $username</td></tr>
				      <tr><td>USBs Deployed:</td><td>$usbCount</td></tr>
				      <tr><td>Start Date:</td><td>$start</td></tr>
				      <tr><td>End Date:</td><td>$end</td></tr>
                                       \n";
                        }
                        mysqli_stmt_close($stmt);
		} else {
                        echo "Error with database query";
                        exit;
                }
                if ($stmt=mysqli_prepare($db, "SELECT user, networks, publicip, home FROM activity WHERE cid = ?")){
                        mysqli_stmt_bind_param($stmt, "i", $cid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $username, $network, $ip, $home);
                        while(mysqli_stmt_fetch($stmt)){
                                $username=htmlspecialchars($username);
                                $network=htmlspecialchars($network);
                                $ip=htmlspecialchars($ip);
                                $home=htmlspecialchars($home);
				echo "<tr><td>User:</td>
					<td>$username</td>
					<td>Public IP:</td>
\					<td> $ip</td>
					<td> Network:</td>
					<td> $network</td>
					<td>  Home: </td>
					<td>$home</td>";
                                echo "</tr> \n";
                        }
                        mysqli_stmt_close($stmt);
		} else {
                        echo "Error with database query";
                        exit;
                }
                echo"</table>";
	break;
	case 1; ##View team
	if($stat !== 'team' AND $stat !== 'lead' AND $_SESSION['status'] !=='Admin'){  		
	     	echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      	echo "Unauthorized Access";
	      	header("refresh:1;url=/index.php");
	      	exit;
	}
	$cid=mysqli_real_escape_string($db,$cid);
	$client=htmlspecialchars($client);
	$location=htmlspecialchars($location);

	if($stat == 'lead' OR $_SESSION['status'] ='Admin'){  		
	        echo "<td><form method=post action=details.php>
	                <input type=hidden name=s value=2>
	                <input type=hidden name=cid value=$cid>
	                <input type=hidden name=client value=$client>
	                <input type=hidden name=location value=$location>
	                <input type=submit name='updateTeam' value='Update Team'>
	              </td></form><br><br>";
	}
	$query="SELECT u.username, t.status FROM users u, teams t WHERE t.cid=? AND t.uid=u.userid";
	echo "<table><tr><td> <b> <u> $location Team Members </b></u></td></tr> \n";
        if($stmt=mysqli_prepare($db,$query)){
                mysqli_stmt_bind_param($stmt,"i",$cid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$user,$status);
                while(mysqli_stmt_fetch($stmt)){
                        $user=$user;
                        $status=$status;
			echo "<tr><td>$user</td>
				<td>$status</td>";
				if($status != 'lead'){
					echo" 
					<td><form method=post action=details.php>
					<input type=hidden name=s value=4> 
	               			<input type=hidden name=cid value=$cid>
					<input type=hidden name=postUser value=$user></td>
					<td><input type=submit name='promote' value='Promote to Team Lead'> 
					</td></tr> </form><br><br>";
				}
		}
		mysqli_stmt_close($stmt);
	}
        echo"</table>";

	break;

	case 2; ##Update team
	if($stat !== 'lead' AND $_SESSION['status'] !=='Admin'){  		
	     	echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      	echo "Unauthorized Access";
	      	header("refresh:1;url=/index.php");
	      	exit;
	}
	$cid=mysqli_real_escape_string($db,$cid);
	$client=htmlspecialchars($client);
	$location=htmlspecialchars($location);
	echo "<table><tr><td> <b> <u> Users </b></u></td></tr> \n
		<form method=post action=details.php>
	        <input type=hidden name=cid value=$cid>
		<input type=hidden name=s value=3>"; 
        $query="SELECT username, userid FROM users WHERE NOT status='Client'";
        $result=mysqli_query($db, $query);
        while($row=mysqli_fetch_row($result)) {
		echo"<tr><td><input type='checkbox' name='team[]' value=$row[1]>$row[0]</td></tr>";
	}
	echo"<tr> <td colspan=2> <input type=submit name=submit value=submit> </td></tr>
             </form></table><br><br>";

	break;	

	case 3; ##Push team update to database
	if($stat !== 'lead' AND $_SESSION['status'] !=='Admin'){  		
	     	echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      	echo "Unauthorized Access";
	      	header("refresh:1;url=/index.php");
	      	exit;
	}
	$cid=mysqli_real_escape_string($db,$cid);
	if (isset($_POST['team'])) {
	    $optionArray = $_POST['team'];
	    for ($i=0; $i<count($optionArray); $i++) {
		    $uid=mysqli_real_escape_string($db,$optionArray[$i]);
		    if($stmt=mysqli_prepare($db, "INSERT INTO teams SET teamid='', uid=?, cid=?, status='team'")){
                        mysqli_stmt_bind_param($stmt, "ii", $uid, $cid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
               	    } else {
                        echo "Error with database query";
                        exit;
                    }

	    }
            echo "$addUser successfully added to team members";
            header( "refresh:3;url=details.php?s=0&cid=$cid" );
	}

	break;

	case 4;		##Promote teammate to lead
	if($stat !== 'lead' AND $_SESSION['status'] !=='Admin'){  		
	     	echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      	echo "Unauthorized Access";
	      	header("refresh:1;url=/index.php");
	      	exit;
	}
	$postUser=mysqli_real_escape_string($db,$postUser);
	$cid=mysqli_real_escape_string($db,$cid);
	if($stmt=mysqli_prepare($db,"SELECT userid FROM users WHERE username=?")){
                mysqli_stmt_bind_param($stmt,"s",$postUser);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$uid);
                while(mysqli_stmt_fetch($stmt)){
                        $uid=$uid;
		}
		mysqli_stmt_close($stmt);
       	} else {
            echo "Error with database query";
            exit;
        }
	if($stmt=mysqli_prepare($db, "UPDATE teams SET status='lead' WHERE uid=? AND cid=?")){
            mysqli_stmt_bind_param($stmt, "ii", $uid, $cid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
       	} else {
            echo "Error with database query";
            exit;
        }

   	echo "$addUser successfully promoted";
    	header( "refresh:2;url=details.php?s=0&cid=$cid" );
	break;

	case 5;		##Team can Input Data
	if($stat !== 'lead' AND $stat !== 'team' AND $_SESSION['status'] !=='Admin'){  		
	     	echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      	echo "Unauthorized Access";
	      	header("refresh:1;url=/index.php");
	      	exit;
	}
	$cid=mysqli_real_escape_string($db,$cid);
	$client=htmlspecialchars($client);
	$location=htmlspecialchars($location);
	          echo "
                <div class='container'>
                  <h2>Input Data</h2>
                  <table class='table'>
                    <thead>
                      <tr>
                       <th>Update data for $location</th>
                      </tr>
                    </thead>
                    <tbody>
                <form method=post action=details.php> 
                        <tr> <td> Update Start Date </td> <td> <input type=date name=addStartDate value=''> </td> </tr>
			<tr> <td> Update End Date </td> <td> <input type=date name=addEndDate value=''> </td> </tr>
                        <tr> <td> Add USBs </td> <td> <input type=number name=addUsb value=''> </td> </tr>
	        	<input type=hidden name=cid value=$cid>
                        <td colspan=2> <input type=hidden name=s value=6> 
                        <input type=submit name=submit class='btn btn-primary' value=submit> </td></tr>
                </tbody></table></div>
                        </form><br> <br>";
	break;

	case 6; 	##Verify and update DB with new data	
	$cid=mysqli_real_escape_string($db,$cid);
	$addUsb=mysqli_real_escape_string($db,$addUsb);
	$addStartDate=mysqli_real_escape_string($db,$addStartDate);
	$addEndDate=mysqli_real_escape_string($db,$addEndDate);
	if($stat !== 'lead' AND $stat !== 'team' AND $_SESSION['status'] !=='Admin'){  		
	     	echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
	      	echo "Unauthorized Access";
	      	header("refresh:1;url=/index.php");
	      	exit;
	}
        if($stmt=mysqli_prepare($db, "UPDATE campaigns SET start=?, end=?, usbcount=usbcount+?")){
                mysqli_stmt_bind_param($stmt, "ssi", $addStartDate, $addEndDate, $addUsb);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
        } else {
                echo "Error with database query";
                exit;
        }
        echo "$addUser successfully updated data";
        header( "refresh:3;url=/index.php" );

	$cid=mysqli_real_escape_string($db,$cid);
	if (isset($_POST['team'])) {
	    $optionArray = $_POST['team'];
	    for ($i=0; $i<count($optionArray); $i++) {
		    $uid=mysqli_real_escape_string($db,$optionArray[$i]);
		    if($stmt=mysqli_prepare($db, "INSERT INTO teams SET teamid='', uid=?, cid=?, status='team'")){
                        mysqli_stmt_bind_param($stmt, "ii", $uid, $cid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
               	    } else {
                        echo "Error with database query";
                        exit;
                    }

	    }
            echo "$addUser successfully added to team members";
            header( "refresh:3;url=/details.php?s=0&cid=$cid" );
	}

	break;

	
}
include_once('footer.php');
?>


