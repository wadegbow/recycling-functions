<?php
require('EditorService.php');

$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

$result = array();

if( !isset($postData['do']) ) { $result['error'] = 'No function name!'; }
if( !isset($postData['args']) ) { $result['error'] = 'No function arguments!'; }

if( !isset($result['error']) ) {

    switch($postData['do']) {
        case 'getAllNews':
          $result['res'] = getAllNews();
          break;
        case 'getActiveNews':
          $result['res'] = getActiveNews();
          break;
        case 'addNews':
          if ($postData['args']['newsArray'] && validateFormHash($postData['args']['csrf'])) {
            $result['res'] = addNews($postData['args']['newsArray']);
          }
          break;
        case 'editNews':
          if ($postData['args']['newsArray'] && validateFormHash($postData['args']['csrf'])) {
            $result['res'] = editNews($postData['args']['newsArray']);
          }
          break;
        case 'deleteNews':
          if ($postData['args']['deleteNews'] && validateFormHash($postData['args']['csrf'])) {
            $result['res'] = deleteNews($newsArray);
          }
          break;

        default:
          $result['error'] = 'Could not find function '.$postData['do'].'!';
          break;
    }
}

echo json_encode($result);

?>
