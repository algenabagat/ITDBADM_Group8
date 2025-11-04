<?php
  /* Change my_username/my_password of ur database here */
  
  $my_serverName = "localhost";
  $my_username = "root";
  $my_password = "DLSU1234!";
  $my_database = "watch_db";
  $my_port = 3306   ;
  
  function getDBConnection($serverName, $username, $password, $database, $port) {
    $conn = new mysqli($serverName, $username, $password, $database, $port);

		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

    return $conn;
  }
?>