<?php
#name: Josh Anderson
#purpose: USB tracker webapp footer
#date: 2017/4/6
#version: 1.0
echo "\n<hr>\n";
#if(isset($_SESSION['authenticated'])){			##If User is logged in, display logout button
#	echo "<tr><td>";
#	if($_SESSION['status']=='Admin'){			##If user is Admin, display user management button
#		echo "<a href=manage.php?s=10> Manage Users |</a></td><td><a href=manage.php?s=20> Manage Campaigns |</a></td><td>";
#	}
#	echo "<a href=index.php?postLog=1>  Logout </a></td></tr> \n<hr>";
#}

echo "
</body>
</html>";
?>

