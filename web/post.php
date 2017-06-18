<?php 

$req_dump=print_r($_POST, TRUE);
$fp=file_put_contents('request.log',$req_dump, FILE_APPEND);

echo "<table>";
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
echo "</table>";


?>
