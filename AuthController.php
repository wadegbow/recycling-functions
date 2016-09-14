<?php

require('AuthService.php');

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$result = array();

if( !isset($postData['do']) ) { $result['error'] = 'No function name!'; }
if( !isset($postData['args']) ) { $result['error'] = 'No function arguments!'; }

if( !isset($result['error']) ) {
    switch($postData['do']) {
        case 'validateAndGet':
          if ($postData['args']['user'] && $postData['args']['pass']) {
            $result = validateAndGet($postData['args']['user'], $postData['args']['pass']);
          } else {
            $result['error'] = 'No username and/or password!';
          }
          break;
        case 'encryptPassword':
          if ($postData['args']) {
            $result = encryptPassword($postData['args'], md5(mt_rand(100000,999999)));
          } else {
            $result['error'] = 'No password was provided so I just kinda sat here and looked puzzled..';
          }
          break;
        case 'validatePassword':
          if ($postData['args']) {
            $result = validatePassword($postData['args']['hash'], $postData['args']['input']);
          } else {
            $result['error'] = 'I need a couple things to compare..';
          }
          break;
        case 'logout':
          if ($postData['args']['logout']) {
            $result = logout();
          }
          break;
        case 'isLoggedIn':
           $result = isLoggedIn();
           break;
        case 'generateFormHash':
          $result['csrf'] = generateFormHash(mt_rand(100000,999999));
          break;
        case 'validateFormHash':
          $result = validateFormHash($postData['args']['csrf']);
          break;

        default:
           $result['error'] = 'Could not find function '.$postData['do'].'!';
           break;
    }
}

echo json_encode($result);

?>
