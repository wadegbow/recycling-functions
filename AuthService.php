<?php
require_once('Recycling.php');

function validateAndGet($username, $password) {
  // Create a new connection to database.
  $fm = connectToDB();
  // Create FileMaker_Command_Find on layout to search
  $findCommand =& $fm->newFindCommand('CMSUsers');
  $findCommand->addFindCriterion('Username', $username);
  // Execute find command
  $result = $findCommand->execute();

  if (FileMaker::isError($result)) {
    header("HTTP/1.1 401 Unauthorized");
    exit;
  } else {
    $records = $result->getRecords();
    $record = $records[0];

    if (validatePassword($record->getField('Password'), $password)) {
      $user['zz__ID'] = $record->getField('zz__ID');
      $user['Username'] = $record->getField('Username');
      $user['Name'] = $record->getField('Name');
      $user['Role'] = $record->getField('Role');
      $user['Session'] = session_id();

      $_SESSION['user_zz__ID'] = $user['zz__ID'];

      return $user;
    } else {
      header("HTTP/1.1 401 Unauthorized");
      exit;
    }
  }
}

function createUser($username, $password) {
  $fm = connectToDB();

  $findCommand =& $fm->newFindCommand('CMSUsers');
  $findCommand->addFindCriterion('Username', $username);

  $result = $findCommand->execute();

  if (FileMaker::isError($result)) {
    return $result->getMessage();
  } else {
    $records = $result->getRecords();
    $record = $records[0];

    $toDB['Salt'] = md5(mt_rand(100000,999999));
    $toDB['Password'] = encryptPassword($password, $toDB['Salt']);

    $editCommand =& $fm->newEditCommand('CMUsers', $record->getRecordId(), $toDB);
    $result = $editCommand->execute();

    if (FileMaker::isError($result)) {
      return "user error";
    } else {
      return "user created";
    }
  }
}

function encryptPassword($password, $salt) {
  return crypt($password, '$6$rounds=5000$'.$salt.'$');
}

function validatePassword($hash, $input) {
  return (crypt($input, $hash) == $hash);
}

function logout() {
  session_destroy();
  return 'session destroyed';
}

function isLoggedIn() {
  return isset($_SESSION['user_zz__ID']);
}

function generateFormHash($key) {
  $hash = hash_hmac('sha512', $key, SECRET_PHRASE);
  $_SESSION['csrf'] = $hash;
  return $hash;
}

?>
