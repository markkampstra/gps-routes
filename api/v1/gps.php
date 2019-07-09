<?php

$db_password = 'm16dyiLsI';
$db_database = 'myshangw_gps';
$db_username = 'myshangw_gps';
$db_host = 'localhost';

$conn = new mysqli($db_host, $db_username, $db_password, $db_database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$post = file_get_contents('php://input');
$date = date('d-m-Y H:i:s');
//echo $post;

$json = json_decode($post);
$lat = $json->lat;
$lon = $json->lon;
$batt = $json->batt;

$log_entry = "\n\n$date\n".$post . "\n";
$log_entry .= $lat . ' ' . $lon . ' ' . $batt . "\n";

file_put_contents('./log.log', $log_entry, FILE_APPEND);

$sql = "INSERT INTO location_entries (lat, lon, batt, raw, created_at)
VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if($stmt) {
	$stmt->bind_param('ddis', $lat, $lon, $batt, $post);
	$stmt->execute();
} else {
	$error = $conn->errno . ' ' . $conn->error;
	echo $error;
}

header("HTTP/1.1 200 OK");
