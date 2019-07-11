<?php
require_once 'config.php';

$conn = new mysqli($db_host, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if (mysqli_connect_errno())
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($conn,"SELECT * FROM location_entries order by created_at desc limit 50");

echo "<table border='1'>
<tr>
<th>lat</th>
<th>lon</th>
<th>batt</th>
<th>created_at</th>
</tr>";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $row['lat'] . "</td>";
echo "<td>" . $row['lon'] . "</td>";
echo "<td>" . $row['batt'] . "</td>";
echo "<td>" . $row['created_at'] . "</td>";
echo "</tr>";
}
echo "</table>";

mysqli_close($conn);
?>
