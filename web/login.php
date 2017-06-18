<?php
#Author : Joshua Anderson
#name   : login.php
#purpose: Allows users to submit their credentials and login
#date   : 2017/3/9
#version: 1.5

include_once('header.php');
?>
<center>
 <form method=post action=index.php>
	<table><tr><td>Username: </td><td><input type=text name=postUser></td></tr>
	<tr><td>Password: </td><td><input type=password name=postPass></td></tr>
	<tr><td><colspan=2><input type=submit name=submit value=Login></td></tr>
	</table>
	</form>
</body>
</html>
<?php
include_once('footer.php');
?>

