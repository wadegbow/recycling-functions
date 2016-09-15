<?php

session_start(); // starts new or resumes existing session

require_once('..\..\filemaker_api\server_data_request.php');
require_once('..\..\filemaker_api\FileMaker.php');
error_reporting(E_ALL);

function connectToDB() {
  return new FileMaker('Recycling', FM_IP, FM_USERNAME, FM_PASSWORD);
}

function validateFormHash ($hash) {
  return $_SESSION['csrf'] === $hash;
}

?>
