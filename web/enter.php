<?php
session_start();
#Author : Joshua Anderson
#name   : enter.php
#purpose: ONLY for USB reporting and recording, should never have human interaction. 
#date   : 2017/3/13
#version: 1.0
include_once('/var/www/html/project-lib.php');
connect($db);

isset ( $_REQUEST['utoken'] ) ? $utoken = strip_tags($_REQUEST['utoken']) : $utoken = "";
isset ( $_REQUEST['usr'] ) ? $usr = strip_tags($_REQUEST['usr']) : $usr = "";
isset ( $_REQUEST['ips'] ) ? $ips = strip_tags($_REQUEST['ips']) : $ips = "";
isset ( $_REQUEST['exip'] ) ? $exip = strip_tags($_REQUEST['exip']) : $exip = "";
isset ( $_REQUEST['home'] ) ? $home = strip_tags($_REQUEST['home']) : $home = "";
isset ( $_REQUEST['utime'] ) ? $utime = strip_tags($_REQUEST['utime']) : $utime = "";

#if($_SERVER['HTTP_USER_AGENT']!="usb"){				##Only our USBs should be here
#                session_destroy();				##Log them out just to be spiteful
#       		 echo "<script type='text/javascript'>alert('You have been logged out');</script>";
#		header("Location: /index.php");
#		exit;
#	}
#}

$utoken=mysqli_real_escape_string($db,$utoken);
$query="SELECT campaignid, ctoken from campaigns where ctoken=?";
if($stmt=mysqli_prepare($db,$query)){
        mysqli_stmt_bind_param($stmt,"s",$utoken);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$cid,$ctoken);
        while(mysqli_stmt_fetch($stmt)){
                $cid=$cid;
                $ctoken=$ctoken;
        }
	if($ctoken==$utoken){
		$usr=mysqli_real_escape_string($db,$usr);
		$ips=mysqli_real_escape_string($db,$ips);
		$exip=mysqli_real_escape_string($db,$exip);
		$home=mysqli_real_escape_string($db,$home);
		if($stmt=mysqli_prepare($db, "INSERT INTO activity SET activityid='',  user=?, networks=?, publicip=?, home=?, cid=?")){
			mysqli_stmt_bind_param($stmt, "ssssi", $usr, $ips, $exip, $home, $cid);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		} else {
			echo "Error with database query";	
			exit;
		}
                echo "Data Recorded";
                header("Location: /index.php");
                exit;
		
	}else {
                echo "Campaign Not Recognized";
                header("Location: /index.php");
                exit;
        }
}
?>

