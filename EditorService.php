<?php
require_once('Recycling.php');

//gets all the news for the dashboard, even inactive articles
function getAllNews() {
  $fm = connectToDB();

  $findCommand =& $fm->newFindCommand('CMSNews');
  $findCommand->addFindCriterion('DatePosted', '*');
  $findCommand->addSortRule('datePosted', 1, FILEMAKER_SORT_DESCEND);
  $result = $findCommand->execute();

  if (FileMaker::isError($result)) {
      echo "Error: " . $result->getMessage() . "\n";
      exit;
  }

  // Get array of found records
  $newsList = $result->getRecords();

  $i = 0;
  foreach ($newsList as $news) {
    foreach($news->getFields() as $field) {
      $newsJSON[$i][$field] = html_entity_decode($news->getField($field));
    }
    $i++;
  }

  return $newsJSON;
}

//gets news marked as active
function getActiveNews() {
  $fm = connectToDB();

  $findCommand =& $fm->newFindCommand('CMSNews');
  $findCommand->addFindCriterion('Status', 'active');
  $findCommand->addFindCriterion('DatePosted', '*');
  $findCommand->addSortRule('datePosted', 1, FILEMAKER_SORT_DESCEND);
  $result = $findCommand->execute();

  if (FileMaker::isError($result)) {
      echo "Error: " . $result->getMessage() . "\n";
      exit;
  }

  // Get array of found records
  $newsList = $result->getRecords();

  $i = 0;
  foreach ($newsList as $news) {
    foreach($news->getFields() as $field) {
      $newsJSON[$i][$field] = html_entity_decode($news->getField($field));
    }
    $i++;
  }

  return $newsJSON;
}

//function for adding news articles to the database
function addNews($newsArray) {
  $fm = connectToDB();

  //create a new record with the newsArray
  $newAdd = $fm->newAddCommand('CMSNews', $newsArray);
  $result = $newAdd->execute();

  //check for errors
  if (FileMaker::isError($result)) {
    return $result->getMessage();
  } else {
    return "success";
  }
}

//function for editing news already in the database
function editNews($newsArray) {
  $fm = connectToDB();

  //find the article with its zz__ID
  $newFind = $fm->newFindCommand('CMSNews');
  $newFind->addFindCriterion('zz__ID', $newsArray['zz__ID']);
  $result = $newFind->execute();

  if (FileMaker::isError($result)) {
    //if the find failed get the error message
    return $result->getMessage();
  } else {
    //if the find was successful get the records
    $records = $result->getRecords();
    //zz__IDs are unique so it should have only found one
    $record = $records[0];
    //get the record ID
    $recID = $record->getRecordId();

    //remove zz__ID and zz__Serial before submitting
    unset($newsArray['zz__ID']);
    unset($newsArray['zz__Serial']);

    //edit the record using the array submitted
    $newEdit = $fm->newEditCommand('CMSNews', $recID, $newsArray);
    $result = $newEdit->execute();

    //check for errors
    if (FileMaker::isError($result)) {
      return $result->getMessage();
    } else {
      return "success";
    }
  }
}

//function for deleting news already in the database
function deleteNews($newsArray) {
  $fm = connectToDB();

  //find the article with its zz__ID
  $newFind = $fm->newFindCommand('CMSNews');
  $newFind->addFindCriterion('zz__ID', $newsArray['zz__ID']);
  $result = $newFind->execute();

  if (FileMaker::isError($result)) {
    return $result->getMessage();
  } else {
    //if the find was successful get the records
    $records = $result->getRecords();
    //zz__IDs are unique so it should have only found one
    $record = $records[0];
    //get the record id
    $recID = $record->getRecordId();

    //delete the record
    $newDelete = $fm->newDeleteCommand('CMSNews', $recID);
    $result = $newDelete->execute();

    //check for errors
    if (FileMaker::isError($result)) {
      return $result->getMessage();
    } else {
      return "success";
    }
  }
}

?>
