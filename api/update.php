<?php
	require_once('../util/config.php');
	$conn = new mysqli($DBServer, $DBUser, $DBPass, $infoDB);
	
	if ($_SERVER['REQUEST_METHOD'] != 'POST'){
		return;
	}
	if(isset($_POST['key']) && $_POST['key'] === "why bother"){
		$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
		$source = $conn->real_escape_string($_POST['source']);
		$msg = $conn->real_escape_string($_POST['message']);
		
		$sql = "INSERT INTO updates (`source`,`message`) VALUES('$source','$msg')";
		$conn->query($sql);
	}
	else{
		http_response_code(500);
		return;
	}
?>