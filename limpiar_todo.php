<?php
$conn = new mysqli("localhost", "root", "", "vintage_motors");
$conn->query("TRUNCATE TABLE favoritos");
echo "Lista vaciada";
?>
