<?php
	$conn = new mysqli("localhost", "root", "", "padismart");

	if($conn->connect_error){
		die("Connection Failed: " .$conn->connect_error);
	}
?>