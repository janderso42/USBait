<?php
#Author : Joshua Anderson
#name   : project.php library file
#purpose: library for dynamic web app
#date   : 2017/2/28
#version: 1.0

#vars
#ID and step vars
isset ( $_REQUEST['s'] ) ? $s = strip_tags($_REQUEST['s']) : $s = "";
isset ( $_REQUEST['client'] ) ? $client = strip_tags($_REQUEST['client']) : $client = "";
isset ( $_REQUEST['Client'] ) ? $Client = strip_tags($_REQUEST['Client']) : $Client = "";
isset ( $_REQUEST['location'] ) ? $location = strip_tags($_REQUEST['location']) : $location = "";
isset ( $_REQUEST['cid'] ) ? $cid = strip_tags($_REQUEST['cid']) : $cid = "";

#Data from USB update
isset ( $_REQUEST['usbUser'] ) ? $usbUser = strip_tags($_REQUEST['usbUser']) : $usbUser = "";
isset ( $_REQUEST['usbAdmin'] ) ? $usbAdmin = strip_tags($_REQUEST['usbAdmin']) : $usbAdmin = "";
isset ( $_REQUEST['usbLocation'] ) ? $usbLocation = strip_tags($_REQUEST['usbLocation']) : $usbLocation = "";
isset ( $_REQUEST['usbNetwork'] ) ? $usbNetowrk = strip_tags($_REQUEST['usbNetwork']) : $usbNetwork = "";
isset ( $_REQUEST['usbCampaign'] ) ? $usbCampaign = strip_tags($_REQUEST['usbCampaign']) : $usbCampagin = "";
isset ( $_REQUEST['usbUserAgent'] ) ? $usbUserAgent = strip_tags($_REQUEST['usbUserAgent']) : $usbUserAgent = "";

#User auth vars
isset ( $_REQUEST['postUser'] ) ? $postUser = strip_tags($_REQUEST['postUser']) : $postUser = "";
isset ( $_REQUEST['postPass'] ) ? $postPass = strip_tags($_REQUEST['postPass']) : $postPass = "";
isset ( $_REQUEST['postLog'] ) ? $postLog = strip_tags($_REQUEST['postLog']) : $postLog = "";

#Add new user vars
isset ( $_REQUEST['addUser'] ) ? $addUser = strip_tags($_REQUEST['addUser']) : $addUser = "";
isset ( $_REQUEST['addUsb'] ) ? $addUsb = strip_tags($_REQUEST['addUsb']) : $addUsb = "";
isset ( $_REQUEST['addStartDate'] ) ? $addStartDate = strip_tags($_REQUEST['addStartDate']) : $addStartDate = "";
isset ( $_REQUEST['addEndDate'] ) ? $addEndDate = strip_tags($_REQUEST['addEndDate']) : $addEndDate = "";
isset ( $_REQUEST['addPass'] ) ? $addPass = $_REQUEST['addPass'] : $addPass = "";
isset ( $_REQUEST['addStatus'] ) ? $addStatus = $_REQUEST['addStatus'] : $addStatus = "";
isset ( $_REQUEST['addEmail'] ) ? $addEmail = strip_tags($_REQUEST['addEmail']) : $addEmail = "";
isset ( $_REQUEST['uid'] ) ? $uid = strip_tags($_REQUEST['uid']) : $uid = "";


function connect(&$db){
	$mycnf="/etc/project-mysql.conf";
 	if (!file_exists($mycnf)) {
		echo "Error file not found: $mycnf";
		exit;
	}
	$mysql_ini_array=parse_ini_file($mycnf);
	$db_host=$mysql_ini_array["host"];
	$db_user=$mysql_ini_array["user"];
	$db_pass=$mysql_ini_array["pass"];
	$db_port=$mysql_ini_array["port"];
	$db_name=$mysql_ini_array["dbName"];
	$db=mysqli_connect($db_host,$db_user,$db_pass,$db_name,$db_port);
	if(!$db) {
		print "Error connecting to DB: " . mysqli_connect_error();
		exit;
	}	

}

function icheck($i) {
	if($i!=null){
		if(!is_numeric($i)){
			print "Error: Invalid input $i";
			exit;
		}
	}
}

function authenticate($db, $postUser, $postPass){
	if($postUser==null || $postPass==null){
		header("Location: /login.php");
		exit;
	}
	$query="select userid, password, salt, status from users where username=?";
	if($stmt=mysqli_prepare($db,$query)){
		mysqli_stmt_bind_param($stmt,"s",$postUser);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt,$userid,$password,$salt, $status);
		while(mysqli_stmt_fetch($stmt)){
			$userid=$userid;
			$password=$password;
			$salt=$salt;
			$status=$status;
		}

		mysqli_stmt_close($stmt);
		$epass=hash('sha256',$postPass.$salt);
		if($epass==$password){
			$_SESSION['usrname']=$postUser;
			$_SESSION['userid']=$userid;
			$_SESSION['status']=$status;
			$_SESSION['authenticated']="yes";
			$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
			header("Refresh:0");
		}else {
			echo "Failed to Login";
			echo "Redirecting to login page...";
			header("refresh:3;url=/login.php");
			exit;
		}
	}
}

function checkAdmin(){
	if("Admin"!=$_SESSION['status']){
               echo "<script type='text/javascript'>alert('You are not allowed to be here.');</script>";
               echo "Unauthorized Access";
       	       header("refresh:1;url=/index.php");
               exit;
        }
}

function listUsers(){		##Not being called anymore 
	checkAdmin();
	echo "<table><tr><td> <b> <u> Users </b></u></td></tr> \n";
                $query="SELECT username, userid FROM users";
                $result=mysqli_query($db, $query);
                while($row=mysqli_fetch_row($result)) {
                                echo "<tr><td> $row[0] </td><td><form><input type=hidden name=s value=92> <input type=hidden name=addUser value=$row[0]><input type=hidden name=uid value=$row[1]> <input type=submit name='resetPass' value='Reset Password'> </td></tr> </form><br><br>";
                }
                echo"</table>";
}

function campaignPermissions($db, $cid){
	$user=$_SESSION['userid'];
	$cid=mysqli_real_escape_string($db,$cid);
	if($stmt=mysqli_prepare($db, "SELECT status FROM teams WHERE uid=? AND cid=?")){
	        mysqli_stmt_bind_param($stmt, "ii", $user, $cid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $status);
		while(mysqli_stmt_fetch($stmt)){
                	$status=htmlspecialchars($status);
		}
		mysqli_stmt_close($stmt);
        } else {
        	echo "Error with database query";
                exit;
        }
	return $status;
}
?>

