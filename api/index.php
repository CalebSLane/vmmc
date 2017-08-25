<?php

$method  = $_SERVER['REQUEST_METHOD'];
$request = trim($_SERVER['PATH_INFO'], '/');
$request_data   = file_get_contents('php://input');
$requestHeaders = apache_request_headers();
$auth = trim($requestHeaders['Authorization']);

file_put_contents('php_index_debug.log', 'index start >'.PHP_EOL, FILE_APPEND | LOCK_EX); 
file_put_contents('php_index_debug.log', "\tmethod: " . $method . PHP_EOL, FILE_APPEND | LOCK_EX); 
file_put_contents('php_index_debug.log', "\trequest: " . $request . PHP_EOL, FILE_APPEND | LOCK_EX); 
file_put_contents('php_index_debug.log', "\trequest data: " . $request_data . PHP_EOL, FILE_APPEND | LOCK_EX);
file_put_contents('php_index_debug.log', "\tauth header: " . $auth . PHP_EOL, FILE_APPEND | LOCK_EX);

//load and connect to MySQL database stuff
require("config.inc.php");

file_put_contents('php_index_debug.log', 'test0 index found config >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
$toss = ob_get_clean(); file_put_contents('php_index_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

//MOVE TO A PROTECTED CONFIG FILE
$secret_key = '0Ba8^BfY6$AjCI@LyGUBz%yyRlEkDEAk';

//extract jwt from auth header (values are base64 encoded)
$jwt = substr($auth, 7);
$jwt_values = explode('.', $jwt);
$jwt_header = $jwt_values[0];
$jwt_payload = $jwt_values[1];
$jwt_recv_sig = $jwt_values[2];

//recompute signature
$jwt_check_sig = base64_encode(hash_hmac('SHA256', "$jwt_header.$jwt_payload", $secret_key, true));

//check if valid signature
if ($jwt_check_sig == $jwt_recv_sig) {
  file_put_contents('php_index_debug.log', 'jwt verified'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();

  if ($method == "GET") {
    
    file_put_contents('php_index_debug.log', 'GET method request received >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    
    switch ($request) {
      
      case 'person':
        $response = getPerson();
        break;
      
      case 'booking':
        $response = getBooking();
        break;
      
      case 'interaction':
        $response = getInteraction();
        break;
      
      case 'region':
        $response = getRegion();
        break;
      
      case 'user_to_acl':
        $response = getUserToAcl();
        break;
      
      case 'user':
        $response = getUser();
        break;
      
      case 'user_type':
        $response = getUserType();
        break;
      
      case 'acl':
        $response = getAcl();
        break;
      
      case 'group_type':
        $response = getGroupType();
        break;
      
      case 'group_activity':
        $response = getGroupActivity();
        break;
      
      case 'location':
        $response = getLocation();
        break;
      
      case 'address':
        $response = getAddress();
        break;
      
      case 'client_table':
        $response = getClient();
        break;
      
      case 'facilitator':
        $response = getFacilitator();
        break;
      
      case 'facilitator_type':
        $response = getFacilitatorType();
        break;
      
      case 'procedure_type':
        $response = getProcedureType();
        break;
      
      case 'followup':
        $response = getFollowup();
        break;
      
      case 'status_type':
        $response = getStatusType();
        break;
      
      case 'institution':
        $response = getInstitution();
        break;
      
      case 'interaction_type':
        $response = getInteractionType();
        break;
      
      case 'assessments_questions':
        $response = getAssessmentsQuestions();
        break;
      
      case 'assessments':
        $response = getAssessments();
        break;
      
      case 'question_dropdown_option':
        $response = getQuestionDropdownOption();
        break;
      
      default:
        file_put_contents('php_index_debug.log', 'default GET > resource "' . $request . '" not found' . PHP_EOL, FILE_APPEND | LOCK_EX);
        $response["success"] = 0;
        $response["message"] = "Invalid resource.";
        die(json_encode($response));
        
    } //$request
    
  } //end $method == "GET"
  else if ($method == "POST") {
    
    file_put_contents('php_index_debug.log', 'POST method request received >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    
    switch ($request) {
      case 'person':
        $response = putPerson();
        break;
      
      case 'booking':
        $response = putBooking();
        break;
      
      case 'client_table':
        $response = putClient();
        break;
      
      case 'facilitator':
        $response = putFacilitator();
        break;
      
      case 'interaction':
        $response = putInteraction();
        break;
      
      case 'user':
        $response = putUser();
        break;
      
      case 'group_activity':
        $response = putGroupActivity();
        break;
      
      case 'person_to_assessments':
        $response = putPersonToAssessments();
        break;
      
      case 'assessments_answers':
        $response = putAssessmentsAnswers();
        break;
      
      case 'geolocation':
        $response = putGeoLocations();
        break;
        
      default:
        file_put_contents('php_index_debug.log', 'default PUT > resource "' . $request . '" not found' . PHP_EOL, FILE_APPEND | LOCK_EX);
        $response["success"] = 0;
        $response["message"] = "Invalid resource.";
        die(json_encode($response));
    } //$request
    
  } //end $method == "POST"

  else if ($method == "PUT") {
    
  } //end $method == "PUT"

  else if ($method == "DELETE") {
    
  } //end $method == "DELETE"
//signature is incorrect. reject request
} else {
  file_put_contents('php_index_debug.log', 'jwt rejected'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
  $response["success"] = 0;
  $response["message"] = "Invalid jwt.";
  die(json_encode($response));
}

function putClient()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putClient()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $timestamp           = $rec[0];
    $first_name          = $rec[1];
    $last_name           = $rec[2];
    $national_id         = $rec[3];
    $phone               = $rec[4];
    $status_id           = $rec[5];
    $loc_id              = $rec[6];
    $latitude            = $rec[7];
    $longitude           = $rec[8];
    $institution_id      = $rec[9];
    $group_activity_name = $rec[10];
    $group_activity_date = $rec[11];
    $address_id          = $rec[12];
    $dob                 = $rec[13];
    $gender              = $rec[14];
    
    file_put_contents('php_index_debug.log', 'putClient()1 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($first_name, $last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectClient($timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, $group_activity_name, $group_activity_date, $address_id, $dob, $gender);
    
    file_put_contents('php_index_debug.log', 'putClient() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $first_name, $last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertClient($timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, $group_activity_name, $group_activity_date, $address_id, $dob, $gender);
      
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updateClient($timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, $group_activity_name, $group_activity_date, $address_id, $dob, $gender);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putCliet() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function putFacilitator()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putFacilitator()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $timestamp           = $rec[0];
    $first_name          = $rec[1];
    $last_name           = $rec[2];
    $national_id         = $rec[3];
    $phone               = $rec[4];
    $facilitator_type_id = $rec[5];
    $note                = $rec[6];
    $location_id         = $rec[7];
    $latitude            = $rec[8];
    $longitude           = $rec[9];
    $institution_id      = $rec[10];
    $address_id          = $rec[11];
    $dob                 = $rec[12];
    $gender              = $rec[13];
    
    file_put_contents('php_index_debug.log', 'putFacilitator()1 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($first_name, $last_name, $timestamp, $address_id, $dob, $gender);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectFacilitator($timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender);
    
    file_put_contents('php_index_debug.log', 'putFacilitator() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $first_name, $last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertFacilitator($timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender);
      
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updateFacilitator($timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putFacilitator() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function selectFacilitator($timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'selectFacilitator1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
timestamp,
first_name,
last_name,
national_id,
phone,
facilitator_type_id,
note,
location_id,
latitude,
longitude,
institution_id,
address_id,
dob,
gender
from facilitator
where 1=1
-- and timestamp <= :timestamp
and first_name = :first_name
and last_name = :last_name
-- and national_id = :national_id
and phone = :phone
-- and format(longitude,5) = format(:longitude,5)
-- and format(latitude,5) = format(:latitude,5) 
  ";
  
  file_put_contents('php_index_debug.log', 'selectFacilitator2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    // $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    // $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    // $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    // $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    // $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    // $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    // $stmt->bindParam(':is_deleted', $is_deleted, PDO::PARAM_STR, strlen($is_deleted));
    
    file_put_contents('php_index_debug.log', 'selectFacilitator3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectFacilitator exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectFacilitator3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function insertFacilitator($timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertFacilitator0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $status_id);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into facilitator 
(timestamp, first_name, last_name, national_id, phone, facilitator_type_id, note, location_id, latitude, longitude, institution_id, address_id, dob, gender)
values ( :timestamp, :first_name, :last_name, :national_id, :phone, :facilitator_type_id, :note, :location_id, :latitude, :longitude, :institution_id, :address_id, :dob, :gender )
  ";
  
  file_put_contents('php_index_debug.log', 'insertFacilitator1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':facilitator_type_id', $facilitator_type_id, PDO::PARAM_STR, strlen($facilitator_type_id));
    $stmt->bindParam(':note', $note, PDO::PARAM_STR, strlen($note));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR, strlen($institution_id));
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertFacilitator2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertFacilitator exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function updateFacilitator($timestamp, $first_name, $last_name, $national_id, $phone, $facilitator_type_id, $note, $location_id, $latitude, $longitude, $institution_id, $address_id, $dob, $gender)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateFacilitator0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $dob);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update facilitator set
timestamp=:timestamp,
first_name=:first_name,
last_name=:last_name,
national_id=:national_id,
phone=:phone,
facilitator_type_id=:facilitator_type_id,
note=:note,
location_id=:location_id,
latitude=:latitude,
longitude=:longitude,
institution_id=:institution_id,
address_id=:address_id,
dob=:dob,
gender=:gender
where 1=1
and timestamp<:wtimestamp
and first_name=:wfirst_name
and last_name=:wlast_name
and phone=:wphone
  ";
  
  file_put_contents('php_index_debug.log', 'updateFacilitator1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':facilitator_type_id', $facilitator_type_id, PDO::PARAM_STR, strlen($facilitator_type_id));
    $stmt->bindParam(':note', $note, PDO::PARAM_STR, strlen($note));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR, strlen($institution_id));
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    $stmt->bindParam(':wtimestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':wfirst_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':wlast_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':wphone', $phone, PDO::PARAM_STR, strlen($phone));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateFacilitator2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateFacilitator exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function selectClient($timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, $group_activity_name, $group_activity_date, $address_id, $dob, $gender)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'selectClient1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
timestamp,
first_name,
last_name,
national_id,
phone,
status_id,
loc_id,
latitude,
longitude,
institution_id,
group_activity_name,
group_activity_date,
address_id,
dob,
gender
from client
where 1=1
-- and timestamp <= :timestamp
and first_name = :first_name
and last_name = :last_name
-- and national_id = :national_id
and phone = :phone
-- and format(longitude,5) = format(:longitude,5)
-- and format(latitude,5) = format(:latitude,5) 
  ";
  
  file_put_contents('php_index_debug.log', 'selectClient2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    // $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    // $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    // $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    // $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    // $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    // $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    // $stmt->bindParam(':is_deleted', $is_deleted, PDO::PARAM_STR, strlen($is_deleted));
    
    file_put_contents('php_index_debug.log', 'selectClient3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectClient exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectClient3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function insertClient($timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, $group_activity_name, $group_activity_date, $address_id, $dob, $gender)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertClient0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $status_id);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into client 
(timestamp, first_name, last_name, national_id, phone, status_id, loc_id, latitude, longitude, institution_id, group_activity_name, group_activity_date, address_id, dob, gender)
values ( :timestamp, :first_name, :last_name, :national_id, :phone, :status_id, :loc_id, :latitude, :longitude, :institution_id, :group_activity_name, :group_activity_date, :address_id, :dob, :gender )
  ";
  
  file_put_contents('php_index_debug.log', 'insertClient1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':status_id', $status_id, PDO::PARAM_STR, strlen($status_id));
    $stmt->bindParam(':loc_id', $loc_id, PDO::PARAM_STR, strlen($loc_id));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR, strlen($institution_id));
    $stmt->bindParam(':group_activity_name', $group_activity_name, PDO::PARAM_STR, strlen($group_activity_name));
    $stmt->bindParam(':group_activity_date', $group_activity_date, PDO::PARAM_STR, strlen($group_activity_date));
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertClient2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertClient exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function updateClient($timestamp, $first_name, $last_name, $national_id, $phone, $status_id, $loc_id, $latitude, $longitude, $institution_id, $group_activity_name, $group_activity_date, $address_id, $dob, $gender)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateClient0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $dob, $group_activity_name, $group_activity_date);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update client set
timestamp=:timestamp,
first_name=:first_name,
last_name=:last_name,
national_id=:national_id,
phone=:phone,
status_id=:status_id,
loc_id=:loc_id,
latitude=:latitude,
longitude=:longitude,
institution_id=:institution_id,
group_activity_name=:group_activity_name,
group_activity_date=:group_activity_date,
address_id=:address_id,
dob=:dob,
gender=:gender
where 1=1
and timestamp<:wtimestamp
and first_name=:wfirst_name
and last_name=:wlast_name
and phone=:wphone
  ";
  
  file_put_contents('php_index_debug.log', 'updateClient1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':status_id', $status_id, PDO::PARAM_STR, strlen($status_id));
    $stmt->bindParam(':loc_id', $loc_id, PDO::PARAM_STR, strlen($loc_id));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR, strlen($institution_id));
    $stmt->bindParam(':group_activity_name', $group_activity_name, PDO::PARAM_STR, strlen($group_activity_name));
    $stmt->bindParam(':group_activity_date', $group_activity_date, PDO::PARAM_STR, strlen($group_activity_date));
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    $stmt->bindParam(':wtimestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':wfirst_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':wlast_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':wphone', $phone, PDO::PARAM_STR, strlen($phone));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateClient2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateClient exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function insertPerson($timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertPerson0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into person 
(timestamp, first_name, last_name, national_id, address_id, phone, dob, gender, latitude, longitude, is_deleted)
values ( :timestamp, :first_name, :last_name, :national_id, :address_id, :phone, :dob, :gender, :latitude, :longitude, :is_deleted )
  ";
  
  file_put_contents('php_index_debug.log', 'insertPerson1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':is_deleted', $is_deleted, PDO::PARAM_STR, strlen($is_deleted));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertPerson2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertPerson exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function insertBooking($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $alt_contact)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertBooking0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $projected_date, '<');
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into booking 
(timestamp, first_name, last_name, national_id, phone, fac_first_name, fac_last_name, fac_national_id, fac_phone, location_id, latitude, longitude, projected_date, actual_date, consent, procedure_type_id, followup_id, followup_date, alt_contact )
values ( :timestamp, :first_name, :last_name, :national_id, :phone, :fac_first_name, :fac_last_name, :fac_national_id, :fac_phone, :location_id, :latitude, :longitude, :projected_date, :actual_date, :consent, :procedure_type_id, :followup_id, :followup_date, :alt_contact )
  ";
  
  file_put_contents('php_index_debug.log', 'insertBooking1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':fac_first_name', $fac_first_name, PDO::PARAM_STR, strlen($fac_first_name));
    $stmt->bindParam(':fac_last_name', $fac_last_name, PDO::PARAM_STR, strlen($fac_last_name));
    $stmt->bindParam(':fac_national_id', $fac_national_id, PDO::PARAM_STR, strlen($fac_national_id));
    $stmt->bindParam(':fac_phone', $fac_phone, PDO::PARAM_STR, strlen($fac_phone));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':projected_date', $projected_date, PDO::PARAM_STR, strlen($projected_date));
    $stmt->bindParam(':actual_date', $actual_date, PDO::PARAM_STR, strlen($actual_date));
    
    $stmt->bindParam(':consent', $consent, PDO::PARAM_STR, strlen($consent));
    $stmt->bindParam(':procedure_type_id', $procedure_type_id, PDO::PARAM_STR, strlen($procedure_type_id));
    $stmt->bindParam(':followup_id', $followup_id, PDO::PARAM_STR, strlen($followup_id));
    $stmt->bindParam(':followup_date', $followup_date, PDO::PARAM_STR, strlen($followup_date));
    $stmt->bindParam(':alt_contact', $alt_contact, PDO::PARAM_STR, strlen($alt_contact));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertBooking2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertBooking exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function updatePerson($timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'updatePerson0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $dob);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update person set
timestamp=:timestamp,
first_name=:first_name,
last_name=:last_name,
national_id=:national_id,
address_id=:address_id,
phone=:phone,
dob=:dob,
gender=:gender,
latitude=:latitude,
longitude=:longitude,
is_deleted=:is_deleted
where 1=1
and timestamp<:wtimestamp
and first_name=:wfirst_name
and last_name=:wlast_name
and phone=:wphone
  ";
  
  file_put_contents('php_index_debug.log', 'updatePerson1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':is_deleted', $is_deleted, PDO::PARAM_STR, strlen($is_deleted));
    $stmt->bindParam(':wtimestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':wfirst_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':wlast_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':wphone', $phone, PDO::PARAM_STR, strlen($phone));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updatePerson2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updatePerson exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function updateBooking($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contact)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateBooking0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contract);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update booking set
timestamp=:timestamp,
first_name=:first_name,
last_name=:last_name,
national_id=:national_id,
phone=:phone,
fac_first_name=:fac_first_name,
fac_last_name=:fac_last_name,
fac_national_id=:fac_national_id,
fac_phone=:fac_phone,
location_id=:location_id,
latitude=:latitude,
longitude=:longitude,
projected_date=:projected_date,
actual_date=:actual_date,
consent=:consent,
procedure_type_id=:procedure_type_id,
followup_id=:followup_id,
followup_date=:followup_date,
alt_contact=:alt_contact
where 1=1
and timestamp<:wtimestamp
and first_name=:wfirst_name
and last_name=:wlast_name
and phone=:wphone
and projected_date=:wprojected_date
  ";
  
  file_put_contents('php_index_debug.log', 'updateBooking1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':fac_first_name', $fac_first_name, PDO::PARAM_STR, strlen($fac_first_name));
    $stmt->bindParam(':fac_last_name', $fac_last_name, PDO::PARAM_STR, strlen($fac_last_name));
    $stmt->bindParam(':fac_national_id', $fac_national_id, PDO::PARAM_STR, strlen($fac_national_id));
    $stmt->bindParam(':fac_phone', $fac_phone, PDO::PARAM_STR, strlen($fac_phone));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':projected_date', $projected_date, PDO::PARAM_STR, strlen($projected_date));
    $stmt->bindParam(':actual_date', $actual_date, PDO::PARAM_STR, strlen($actual_date));
    
    $stmt->bindParam(':consent', $consent, PDO::PARAM_STR, strlen($consent));
    $stmt->bindParam(':procedure_type_id', $procedure_type_id, PDO::PARAM_STR, strlen($procedure_type_id));
    $stmt->bindParam(':followup_id', $followup_id, PDO::PARAM_STR, strlen($followup_id));
    $stmt->bindParam(':followup_date', $followup_date, PDO::PARAM_STR, strlen($followup_date));
    $stmt->bindParam(':alt_contact', $alt_contact, PDO::PARAM_STR, strlen($alt_contact));
    
    $stmt->bindParam(':wtimestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':wfirst_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':wlast_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':wphone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':wprojected_date', $projected_date, PDO::PARAM_STR, strlen($projected_date));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateBooking2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateBooking exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function putPerson()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putPerson0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $timestamp   = $rec[0];
    $first_name  = $rec[1];
    $last_name   = $rec[2];
    $national_id = $rec[3];
    $address_id  = $rec[4];
    $phone       = $rec[5];
    $dob         = $rec[6];
    $gender      = $rec[7];
    $latitude    = $rec[8];
    $longitude   = $rec[9];
    $is_deleted  = $rec[10];
    
    file_put_contents('php_index_debug.log', 'putPerson() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($first_name, $last_name, $dob);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectPerson($timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted);
    
    file_put_contents('php_index_debug.log', 'putPerson() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $first_name, $last_name, $dob);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertPerson($timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted);
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updatePerson($timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putPerson() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function putBooking()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putBooking0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $timestamp         = $rec[0];
    $first_name        = $rec[1];
    $last_name         = $rec[2];
    $national_id       = $rec[3];
    $phone             = $rec[4];
    $fac_first_name    = $rec[5];
    $fac_last_name     = $rec[6];
    $fac_national_id   = $rec[7];
    $fac_phone         = $rec[8];
    $location_id       = $rec[9];
    $latitude          = $rec[10];
    $longitude         = $rec[11];
    $projected_date    = $rec[12];
    $actual_date       = $rec[13];
    $consent           = $rec[14];
    $procedure_type_id = $rec[15];
    $followup_id       = $rec[16];
    $followup_date     = $rec[17];
    $alt_contact       = $rec[18];
    
    file_put_contents('php_index_debug.log', 'putBooking1() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contact);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectBooking($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contact);
    
    file_put_contents('php_index_debug.log', 'putBooking() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $first_name, $last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertBooking($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contact);
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updateBooking($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contact);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putBooking() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function putGeoLocations(){

    global $db, $request_data;

    $post = array();
    //$post = $_POST['recs'];

    file_put_contents('php_debug.log', 'putGeoLocations0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("post['datatable']: ", $_POST['datatable'], "END");
    //var_dump('$_POST: ', $_POST, "END");
    //var_dump('$_POST num ', $_POST['num_recs'], "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

     
    for($i = 0; $i < $_POST['num_recs']; $i++){

        $recsKey = 'recs'.$i;
        $rec = explode(',', $_POST[$recsKey]);
        $longitude =     $rec[0];
        $latitude =      $rec[1];
        $device_id =     $rec[2];
        $created_at =    $rec[3];
        $username =      $rec[4];
        $password =      $rec[5];
    
    file_put_contents('php_index_debug.log', 'putGeoLocations() recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($longitude, $latitude, $device_id, $created_at, $username, $password);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = getGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password);
    
    if (!$row) {
      insertGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password);
    } //!$row
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putGeoLocations() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function selectPerson($timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'selectPerson1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $is_deleted, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
timestamp,
first_name,
last_name,
national_id,
address_id,
phone,
dob,
gender,
latitude,
longitude,
is_deleted
from person
where 1=1
-- and timestamp <= :timestamp
and first_name = :first_name
and last_name = :last_name
-- and national_id = :national_id
-- and address_id = :address_id
and phone = :phone
-- and dob =:dob
-- and gender =:gender
-- and format(longitude,5) = format(:longitude,5)
-- and format(latitude,5) = format(:latitude,5) 
-- and is_deleted = :is_deleted
  ";
  
  file_put_contents('php_index_debug.log', 'selectPerson2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    // $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    // $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    // $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    // $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    // $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    // $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    // $stmt->bindParam(':is_deleted', $is_deleted, PDO::PARAM_STR, strlen($is_deleted));
    
    file_put_contents('php_index_debug.log', 'selectPerson3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectPerson exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectPerson3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function selectBooking($timestamp, $first_name, $last_name, $national_id, $phone, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $location_id, $latitude, $longitude, $projected_date, $actual_date, $consent, $procedure_type_id, $followup_id, $followup_date, $alt_contact)
{
  global $db;
  
  
  file_put_contents('php_index_debug.log', 'selectBooking1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $timestamp, $first_name, $last_name, $national_id, $address_id, $phone, $dob, $gender, $latitude, $longitude, $followup_date, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
timestamp,
first_name,
last_name,
national_id,
phone,
fac_first_name,
fac_last_name,
fac_national_id,
fac_phone,
location_id,
latitude,
longitude,
projected_date,
actual_date,
consent,
procedure_type_id,
followup_id,
followup_date,
alt_contact
from booking
where 1=1
-- and timestamp <= :timestamp
and first_name = :first_name
and last_name = :last_name
-- and national_id = :national_id
and phone = :phone
and projected_date = :projected_date
-- and format(longitude,5) = format(:longitude,5)
-- and format(latitude,5) = format(:latitude,5) 
  ";
  
  file_put_contents('php_index_debug.log', 'selectBooking2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    // $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    // $stmt->bindParam(':address_id', $address_id, PDO::PARAM_STR, strlen($address_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':projected_date', $projected_date, PDO::PARAM_STR, strlen($projected_date));
    // $stmt->bindParam(':dob', $dob, PDO::PARAM_STR, strlen($dob));
    // $stmt->bindParam(':gender', $gender, PDO::PARAM_STR, strlen($gender));
    // $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    // $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    // $stmt->bindParam(':is_deleted', $is_deleted, PDO::PARAM_STR, strlen($is_deleted));
    
    file_put_contents('php_index_debug.log', 'selectBooking3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectBooking exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectBooking3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function getGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'getGeoLocations1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
longitude,
latitude,
device_id,
created_at,
username,
password
from geolocations
where format(longitude,5) = format(:longitude,5)
and format(latitude,5) = format(:latitude,5) 
and device_id = :device_id
and created_at = :created_at
and username = :username
and password = :password
  ";
  
  file_put_contents('php_index_debug.log', 'getGeoLocations2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  // var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':device_id', $device_id, PDO::PARAM_STR, strlen($device_id));
    $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR, strlen($created_at));
    $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
    $stmt->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
    
    //file_put_contents('php_index_debug.log', 'getGeoLocations3a >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    //var_dump("queryString=", $stmt->queryString, "END");
    //$toss = ob_get_clean(); file_put_contents('php_index_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'getGeoLocations exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'getGeoLocations3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function insertGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertGeoLocations0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($longitude, $latitude, $device_id, $created_at, $username, $password);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into geolocations
(longitude, latitude, device_id, created_at, username, password)
values ( :longitude, :latitude, :device_id, :created_at, :username, :password )
  ";
  
  file_put_contents('php_index_debug.log', 'insertGeoLocations1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':device_id', $device_id, PDO::PARAM_STR, strlen($device_id));
    $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR, strlen($created_at));
    $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
    $stmt->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertGeoLocations2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertGeoLocations exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
  
}

function getAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getAssessmentsAnswers1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($person, $facility, $date_created, $assessment_id, $question);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
person,
facility,
date_created,
assessment_id,
question,
`option`          
from assess
where person = :person
and facility = :facility 
and date_created = :date_created
and assessment_id = :assessment_id
and question = :question
  ";
  
  try {
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':person', $person, PDO::PARAM_INT);
    $stmt->bindParam(':facility', $facility, PDO::PARAM_INT);
    $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);
    $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);
    $stmt->bindParam(':question', $question, PDO::PARAM_INT);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'getAssessmentsAnswers exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  return $row;
}

function insertAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertAssessmentsAnswers0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($person, $facility, $date_created, $assessment_id, $question, $answer);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into assess
(person, facility, date_created, assessment_id, question, `option`, active)
values ( :person, :facility, :date_created, :assessment_id, :question, :answer, 'Y' )
  ";
  
  file_put_contents('php_index_debug.log', 'insertAssessmentsAnswers1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':person', $person, PDO::PARAM_INT);
    $stmt->bindParam(':facility', $facility, PDO::PARAM_INT);
    $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);
    $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);
    $stmt->bindParam(':question', $question, PDO::PARAM_INT);
    $stmt->bindParam(':answer', $answer, PDO::PARAM_STR, strlen($answer));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertAssessmentsAnswers2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertAssessmentsAnswers exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function updateAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateAssessmentsAnswers0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($answer);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update assess set 
`option` = :answer
where 1=1
and person = :person
and facility = :facility
and date_created = :date_created
and assessment_id = :assessment_id
and question = :question
  ";
  
  file_put_contents('php_index_debug.log', 'updateAssessmentsAnswers1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    $stmt->bindParam(':answer', $answer, PDO::PARAM_STR, strlen($answer));
    $stmt->bindParam(':person', $person, PDO::PARAM_INT);
    $stmt->bindParam(':facility', $facility, PDO::PARAM_INT);
    $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);
    $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);
    $stmt->bindParam(':question', $question, PDO::PARAM_INT);
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateAssessmentsAnswers2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateAssessmentsAnswers exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function putAssessmentsAnswers() {
   global $db, $request_data;

   $post = array();
   //$post = $_POST['recs'];

   file_put_contents('php_debug.log', 'putAssessmentsAnswers0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("post['datatable']: ", $_POST['datatable'], "END");
   //var_dump('$_POST: ', $_POST, "END");
   //var_dump('$_POST num ', $_POST['num_recs'], "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   
   for($i = 0; $i < $_POST['num_recs']; $i++){

      $recsKey = 'recs'.$i;
      $rec = explode('|', $_POST[$recsKey]);
      $assess_id =     $rec[0];
      $person =        $rec[1];
      $facility =      $rec[2];
      $date_created =  $rec[3];
      $assessment_id = $rec[4];
      $question =      $rec[5];
      $answer =        $rec[6];
    
    file_put_contents('php_index_debug.log', 'putAssessmentsAnswers recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($person, $facility, $date_created, $assessment_id, $question, $answer);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = getAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question);
    
    file_put_contents('php_index_debug.log', 'putAssessmentsAnswers returned >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump('row: ', $row);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer);
    } //!$row
    elseif (strcmp($row['option'], $answer)) { // exists, answer changed
      updateAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer);
    } //strcmp($row['option'], $answer)
    else {
      file_put_contents('php_index_debug.log', 'putAssessmentsAnswers no change >' . PHP_EOL, FILE_APPEND | LOCK_EX);
      ob_start();
      $toss = ob_get_clean();
      file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putAssessmentsAnswers DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function getPerson()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getPerson()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
p.id,
p.timestamp,
p.first_name,
p.last_name,
ifnull(p.national_id, 'not available') as national_id,
p.address_id,
p.phone,
p.dob,
p.gender,
p.latitude,
p.longitude,
p.is_deleted
from person p
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getPerson()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getPerson() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getPerson()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getPerson()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                = array();
      $post["id"]          = $row["id"];
      $post["timestamp"]   = $row["timestamp"];
      $post["first_name"]  = $row["first_name"];
      $post["last_name"]   = $row["last_name"];
      $post["national_id"] = $row["national_id"];
      $post["address_id"]  = $row["address_id"];
      $post["phone"]       = $row["phone"];
      $post["dob"]         = $row["dob"];
      $post["gender"]      = $row["gender"];
      $post["latitude"]    = $row["latitude"];
      $post["longitude"]   = $row["longitude"];
      $post["is_deleted"]  = $row["is_deleted"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getFacilitator()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getFacilitator()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
f.id,
f.timestamp,
f.first_name,
f.last_name,
f.national_id,
f.phone,
f.facilitator_type_id,
f.note,
f.location_id,
f.latitude,
f.longitude,
f.institution_id,
f.address_id,
f.dob,
f.gender
from facilitator f
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getFacilitator()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = "0";
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getFacilitator() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getFacilitator()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getFacilitator()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                = array();
      $post["id"]          = $row["id"];
      $post["timestamp"]   = $row["timestamp"];
      $post["first_name"]  = $row["first_name"];
      $post["last_name"]   = $row["last_name"];
      $post["national_id"] = $row["national_id"];
      $post["phone"]       = $row["phone"];
      
      $post["facilitator_type_id"] = $row["facilitator_type_id"];
      $post["note"]                = $row["note"];
      $post["location_id"]         = $row["location_id"];
      $post["latitude"]            = $row["latitude"];
      $post["longitude"]           = $row["longitude"];
      $post["institution_id"]      = $row["institution_id"];
      $post["address_id"]          = $row["address_id"];
      $post["dob"]                 = $row["dob"];
      $post["gender"]              = $row["gender"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getBooking()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getBooking()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
b.id,
b.timestamp,
b.first_name,
b.last_name,
ifnull(b.national_id, 'not available') as national_id,
b.phone,
b.fac_first_name,
b.fac_last_name,
ifnull(b.fac_national_id, 'not available') as fac_national_id,
b.fac_phone,
b.location_id,
b.latitude,
b.longitude,
b.projected_date,
b.actual_date,
b.consent,
b.procedure_type_id,
b.followup_id,
b.followup_date,
b.alt_contact
from booking b
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getBooking()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = "0";
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getBooking() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getBooking()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getBooking()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                      = array();
      $post["id"]                = $row["id"];
      $post["timestamp"]         = $row["timestamp"];
      $post["first_name"]        = $row["first_name"];
      $post["last_name"]         = $row["last_name"];
      $post["national_id"]       = $row["national_id"];
      $post["phone"]             = $row["phone"];
      $post["fac_first_name"]    = $row["fac_first_name"];
      $post["fac_last_name"]     = $row["fac_last_name"];
      $post["fac_national_id"]   = $row["fac_national_id"];
      $post["fac_phone"]         = $row["fac_phone"];
      $post["location_id"]       = $row["location_id"];
      $post["latitude"]          = $row["latitude"];
      $post["longitude"]         = $row["longitude"];
      $post["projected_date"]    = $row["projected_date"];
      $post["actual_date"]       = $row["actual_date"];
      $post["consent"]           = $row["consent"];
      $post["procedure_type_id"] = $row["procedure_type_id"];
      $post["followup_id"]       = $row["followup_id"];
      $post["followup_date"]     = $row["followup_date"];
      $post["alt_contact"]       = $row["alt_contact"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getRegion()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getRegion()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
r.id,
r.name
from region r
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getRegion()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getRegion() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getRegion()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getRegion()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getAcl()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getAcl()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
a.id,
a.acl
from acl a
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getAcl()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getAcl() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getAcl()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getAcl()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post        = array();
      $post["id"]  = $row["id"];
      $post["acl"] = $row["acl"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getUserType()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getUserType()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
ut.id,
ut.name
from user_type ut
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getUserType()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getUserType() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getUserType()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getUserType()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getUserToAcl()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getUserToAcl()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
uta.id,
uta.acl_id,
uta.user_id,
uta.created_by,
uta.timestamp_created
from user_to_acl uta
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getUserToAcl()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getUserToAcl() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getUserToAcl()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getUserToAcl()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                      = array();
      $post["id"]                = $row["id"];
      $post["acl_id"]            = $row["acl_id"];
      $post["user_id"]           = $row["user_id"];
      $post["created_by"]        = $row["created_by"];
      $post["timestamp_created"] = $row["timestamp_created"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getUser()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getUser()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
    select
      u.id,
      u.timestamp,
      u.username,
      u.password,
      u.email,
      u.first_name,
      u.last_name,
      u.national_id,
      u.phone,
      u.region_id,
      u.user_type_id,
      u.location_id,
      u.modified_by,
      u.created_by,
      u.is_blocked,
      u.timestamp_updated,
      u.timestamp_created,
      u.timestamp_last_login
    from user u
    where 1=1 
  ";
  
  file_put_contents('php_index_debug.log', 'getUser()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getUser() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getUser()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getUser()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post       = array();
      $post["id"] = $row["id"];
      
      $post["username"]             = $row["username"];
      $post["timestamp"]            = $row["timestamp"];
      $post["password"]             = $row["password"];
      $post["email"]                = $row["email"];
      $post["first_name"]           = $row["first_name"];
      $post["last_name"]            = $row["last_name"];
      $post["national_id"]          = $row["national_id"];
      $post["phone"]                = $row["phone"];
      $post["region_id"]            = $row["region_id"];
      $post["user_type_id"]         = $row["user_type_id"];
      $post["location_id"]          = $row["location_id"];
      $post["modified_by"]          = $row["modified_by"];
      $post["created_by"]           = $row["created_by"];
      $post["is_blocked"]           = $row["is_blocked"];
      $post["timestamp_updated"]    = $row["timestamp_updated"];
      $post["timestamp_created"]    = $row["timestamp_created"];
      $post["timestamp_last_login"] = $row["timestamp_last_login"];
      
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getLocation()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getLocation()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
l.id,
l.name,
l.region_id
from location l
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getLocation()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getLocation() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getLocation()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getLocation()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post              = array();
      $post["id"]        = $row["id"];
      $post["name"]      = $row["name"];
      $post["region_id"] = $row["region_id"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getAddress()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getAddress()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
a.id,
a.name,
a.region_id
from address a
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getAddress()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getAddress() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getAddress()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getAddress()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post              = array();
      $post["id"]        = $row["id"];
      $post["name"]      = $row["name"];
      $post["region_id"] = $row["region_id"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getClient()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getClient()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
c.id,
c.timestamp,
c.first_name,
c.last_name,
c.national_id,
c.phone,
c.status_id,
c.loc_id,
c.latitude,
c.longitude,
c.institution_id,
c.group_activity_name,
c.group_activity_date,
c.address_id,
c.dob,
c.gender
from client c
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getClient()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getClient() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getClient()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getClient()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                        = array();
      $post["id"]                  = $row["id"];
      $post["timestamp"]           = $row["timestamp"];
      $post["first_name"]          = $row["first_name"];
      $post["last_name"]           = $row["last_name"];
      $post["national_id"]         = $row["national_id"];
      $post["phone"]               = $row["phone"];
      $post["status_id"]           = $row["status_id"];
      $post["loc_id"]              = $row["loc_id"];
      $post["latitude"]            = $row["latitude"];
      $post["longitude"]           = $row["longitude"];
      $post["institution_id"]      = $row["institution_id"];
      $post["group_activity_name"] = $row["group_activity_name"];
      $post["group_activity_date"] = $row["group_activity_date"];
      $post["address_id"]          = $row["address_id"];
      $post["dob"]                 = $row["dob"];
      $post["gender"]              = $row["gender"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function putUser()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putUser()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $timestamp            = $rec[0];
    $username             = $rec[1];
    $password             = $rec[2];
    $email                = $rec[3];
    $first_name           = $rec[4];
    $last_name            = $rec[5];
    $national_id          = $rec[6];
    $phone                = $rec[7];
    $region_id            = $rec[8];
    $user_type_id         = $rec[9];
    $location_id          = $rec[10];
    $modified_by          = $rec[11];
    $created_by           = $rec[12];
    $is_blocked           = $rec[13];
    $timestamp_updated    = $rec[14];
    $timestamp_created    = $rec[15];
    $timestamp_last_login = $rec[16];
    
    file_put_contents('php_index_debug.log', 'putUser()1 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($name, $activity_date);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectUser($id, $timestamp, $username, $password, $email, $first_name, $last_name, $national_id, $phone, $region_id, $user_type_id, $location_id, $modified_by, $created_by, $is_blocked, $timestamp_updated, $timestamp_created, $timestamp_last_login);
    
    file_put_contents('php_index_debug.log', 'putUser()2 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $fac_first_name, $fac_last_name, $person_first_name, $person_last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertUser($id, $timestamp, $username, $password, $email, $first_name, $last_name, $national_id, $phone, $region_id, $user_type_id, $location_id, $modified_by, $created_by, $is_blocked, $timestamp_updated, $timestamp_created, $timestamp_last_login);
      
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updateUser($id, $timestamp, $username, $password, $email, $first_name, $last_name, $national_id, $phone, $region_id, $user_type_id, $location_id, $modified_by, $created_by, $is_blocked, $timestamp_updated, $timestamp_created, $timestamp_last_login);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putUser() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function putGroupActivity()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putGroupActivity()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $name           = $rec[0];
    $timestamp      = $rec[1];
    $location_id    = $rec[2];
    $activity_date  = $rec[3];
    $group_type_id  = $rec[4];
    $institution_id = $rec[5];
    $males          = $rec[6];
    $females        = $rec[7];
    $messages       = $rec[8];
    $latitude       = $rec[9];
    $longitude      = $rec[10];
    
    file_put_contents('php_index_debug.log', 'putGroupActivity()1 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($name, $activity_date);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectGroupActivity($id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude);
    
    file_put_contents('php_index_debug.log', 'putGroupActivity()2 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $fac_first_name, $fac_last_name, $person_first_name, $person_last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertGroupActivity($id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude);
      
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updateGroupActivity($id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putGroupActivity() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function getGroupActivity()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getGroupActivity()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
ga.id,
ga.name,
ga.timestamp,
ga.location_id,
ga.activity_date,
ga.group_type_id,
ga.institution_id,
ga.males,
ga.females,
ga.messages,
ga.latitude,
ga.longitude
from group_activity ga
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getGroupActivity()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getGroupActivity() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getGroupActivity()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getGroupActivity()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                   = array();
      $post["id"]             = $row["id"];
      $post["name"]           = $row["name"];
      $post["timestamp"]      = $row["timestamp"];
      $post["location_id"]    = $row["location_id"];
      $post["activity_date"]  = $row["activity_date"];
      $post["group_type_id"]  = $row["group_type_id"];
      $post["institution_id"] = $row["institution_id"];
      $post["males"]          = $row["males"];
      $post["females"]        = $row["females"];
      $post["messages"]       = $row["messages"];
      $post["latitude"]       = $row["latitude"];
      $post["longitude"]      = $row["longitude"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getFacilitatorType()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getFacilitatorType()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
f.id,
f.name
from facilitator_type f
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getFacilitatorType()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getFacilitatorType() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getFacilitatorType()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getFacilitatorType()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getProcedureType()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getProcedureType()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
p.id,
p.name
from procedure_type p
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getProcedureType()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getProcedureType() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getProcedureType()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getProcedureType()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getFollowup()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getFollowup()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
f.id,
f.name
from followup f
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getFollowup()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getFollowup() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getFollowup()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getFollowup()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getStatusType()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getStatusType()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
st.id,
st.name
from status_type st 
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getStatusType()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getStatusType() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getStatusType()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getStatusType()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getGroupType()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getGroupType()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
gt.id,
gt.name
from group_type gt
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getGroupType()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getGroupType() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getGroupType()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getGroupType()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getInstitution()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getInstitution()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
i.id,
i.name,
i.region_id
from institution i
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getInstitution()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getInstitution() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getInstitution()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getInstitution()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post              = array();
      $post["id"]        = $row["id"];
      $post["name"]      = $row["name"];
      $post["region_id"] = $row["region_id"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getInteractionType()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getInteractionType()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
i.id,
i.name
from interaction_type i
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getInteractionType()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getInteractionType() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getInteractionType()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getInteractionType()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post         = array();
      $post["id"]   = $row["id"];
      $post["name"] = $row["name"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getAssessmentsQuestions()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getAssessmentsQuestions()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
aq.id,
aq.assessment_id,
aq.question,
aq.itemorder,
aq.itemtype,
aq.status
from assessments_questions aq
   ";
  
  file_put_contents('php_index_debug.log', 'getAssessmentsQuestions()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getAssessmentsQuestions() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    var_dump("ex=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getAssessmentsQuestions()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getAssessmentsQuestions()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                             = array();
      $post["assessments_questions_id"] = $row["id"];
      $post["assessment_id"]            = $row["assessment_id"];
      $post["question"]                 = $row["question"];
      $post["itemorder"]                = $row["itemorder"];
      $post["itemtype"]                 = $row["itemtype"];
      $post["status"]                   = $row["status"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function getAssessments()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getAssessments()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select 
  a.id, 
  lat.assessment_type, 
  status 
from assessments a
join lookup_assessment_types lat on a.assessment_type_id = lat.id
   ";
  
  file_put_contents('php_index_debug.log', 'getAssessments()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getAssessments() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getAssessments()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getAssessments()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                    = array();
      $post["assessment_id"]   = $row["id"];
      $post["assessment_type"] = $row["assessment_type"];
      $post["status"]          = $row["status"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function putInteraction()
{
  
  global $db, $request_data;
  
  $post = array();
  //$post = $_POST['recs'];
  
  file_put_contents('php_index_debug.log', 'putInteraction()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
//  var_dump("post['datatable']: ", $_POST['datatable'], "END");
//  var_dump('$_POST: ', $_POST, "END");
//  var_dump('$_POST num ', $_POST['num_recs'], "END");
//  $toss = ob_get_clean();
//  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  
  $req_data = json_decode($request_data, true);
  $rec = array();  
  foreach($req_data['datatable'] as $recs) {
    $i = 0;    
    foreach ($recs as $value) {
      $rec[$i] = $value;
      $i = $i + 1;
    }
    ob_start();
    var_dump($rec);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $timestamp          = $rec[0];
    $fac_first_name     = $rec[1];
    $fac_last_name      = $rec[2];
    $fac_national_id    = $rec[3];
    $fac_phone          = $rec[4];
    $person_first_name  = $rec[5];
    $person_last_name   = $rec[6];
    $person_national_id = $rec[7];
    $person_phone       = $rec[8];
    $interaction_date   = $rec[9];
    $followup_date      = $rec[10];
    $type_id            = $rec[11];
    $note               = $rec[12];
    
    file_put_contents('php_index_debug.log', 'putInteraction()1 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($fac_first_name, $fac_last_name, $person_first_name, $person_last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $row = selectInteraction($timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note);
    
    file_put_contents('php_index_debug.log', 'putInteraction()2 recs >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump($timestamp, '==', $row[timestamp], $fac_first_name, $fac_last_name, $person_first_name, $person_last_name);
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    if (!$row) {
      insertInteraction($timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note);
      
    } //!$row
    elseif ($row[timestamp] < $timestamp) {
      updateInteraction($timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note);
    } //$row[timestamp] < $timestamp
  } //$i = 0; $i < $_POST['num_recs']; $i++
  
  file_put_contents('php_index_debug.log', 'putInteraction() DONE >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $response["success"] = 1;
  die(json_encode($response));
}

function getInteraction()
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'getInteraction()0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("_POST=", $_POST, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = " 
select
i.id,
i.timestamp,
i.fac_first_name,
i.fac_last_name,
i.fac_national_id,
i.fac_phone,
i.person_first_name,
i.person_last_name,
i.person_national_id,
i.person_phone,
i.interaction_date, 
i.followup_date, 
i.type_id, 
i.note
from interaction i
where 1=1 
   ";
  
  file_put_contents('php_index_debug.log', 'getInteraction()1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query_params = array();
  
  try {
    $stmt   = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch (PDOException $ex) {
    // For testing, you could use a die and message. 
    //die("Failed to run query: " . $ex->getMessage());
    //or just use this use this one to product JSON data:
    $response["success"] = 0;
    $response["message"] = "Database Error.";
    
    file_put_contents('php_index_debug.log', 'getInteraction() exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("response=", $response, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    die(json_encode($response));
  }
  
  file_put_contents('php_index_debug.log', 'getInteraction()2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("result=", $result, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $rows = $stmt->fetchAll();
  
  file_put_contents('php_index_debug.log', 'getInteraction()2a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  //var_dump("rows=", $rows, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  if ($rows) {
    $response["success"]        = 1;
    $response["number_records"] = count($rows);
    $response["posts"]          = array();
    
    foreach ($rows as $row) {
      $post                       = array();
      $post["id"]                 = $row["id"];
      $post["timestamp"]          = $row["timestamp"];
      $post["fac_first_name"]     = $row["fac_first_name"];
      $post["fac_last_name"]      = $row["fac_last_name"];
      $post["fac_national_id"]    = $row["fac_national_id"];
      $post["fac_phone"]          = $row["fac_phone"];
      $post["person_first_name"]  = $row["person_first_name"];
      $post["person_last_name"]   = $row["person_last_name"];
      $post["person_national_id"] = $row["person_national_id"];
      $post["person_phone"]       = $row["person_phone"];
      $post["interaction_date"]   = $row["interaction_date"];
      $post["followup_date"]      = $row["followup_date"];
      $post["type_id"]            = $row["type_id"];
      $post["note"]               = $row["note"];
      array_push($response["posts"], $post);
    } //$rows as $row
    
    die(json_encode($response));
  } //$rows
}

function selectInteraction($timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'selectInteraction1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
id,
timestamp,
fac_first_name,
fac_last_name,
fac_national_id,
fac_phone,
person_first_name,
person_last_name,
person_national_id,
person_phone,
interaction_date, 
followup_date, 
type_id, 
note
from interaction
where 1=1 
-- and timestamp <= :timestamp
and fac_first_name = :fac_first_name
and fac_last_name = :fac_last_name
-- and fac_national_id = :fac_national_id
and fac_phone = :fac_phone
and person_first_name = :person_first_name
and person_last_name = :person_last_name
-- and person_national_id = :person_national_id
and person_phone = :person_phone
and interaction_date = :interaction_date
-- and followup_date = :followup_date
-- and type_id = :type_id
  ";
  
  file_put_contents('php_index_debug.log', 'selectInteraction2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':fac_first_name', $fac_first_name, PDO::PARAM_STR, strlen($fac_first_name));
    $stmt->bindParam(':fac_last_name', $fac_last_name, PDO::PARAM_STR, strlen($fac_last_name));
    // $stmt->bindParam(':fac_national_id', $fac_national_id, PDO::PARAM_STR, strlen($fac_national_id));
    $stmt->bindParam(':fac_phone', $fac_phone, PDO::PARAM_STR, strlen($fac_phone));
    $stmt->bindParam(':person_first_name', $person_first_name, PDO::PARAM_STR, strlen($person_first_name));
    $stmt->bindParam(':person_last_name', $person_last_name, PDO::PARAM_STR, strlen($person_last_name));
    // $stmt->bindParam(':person_national_id', $fac_national_id, PDO::PARAM_STR, strlen($person_national_id));
    $stmt->bindParam(':person_phone', $person_phone, PDO::PARAM_STR, strlen($person_phone));
    $stmt->bindParam(':interaction_date', $interaction_date, PDO::PARAM_STR, strlen($interaction_date));
    // $stmt->bindParam(':followup_date', $followup_date, PDO::PARAM_STR, strlen($followup_date));
    
    file_put_contents('php_index_debug.log', 'selectInteraction3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectInteraction exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectInteraction3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function insertInteraction($timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertInteraction0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $status_id);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into interaction ( timestamp, fac_first_name, fac_last_name, fac_national_id, fac_phone, person_first_name, person_last_name, person_national_id, person_phone, interaction_date, followup_date, type_id, note)
values ( :timestamp, :fac_first_name, :fac_last_name, :fac_national_id, :fac_phone, :person_first_name, :person_last_name, :person_national_id, :person_phone, :interaction_date, :followup_date, :type_id, :note)
  ";
  
  file_put_contents('php_index_debug.log', 'insertInteraction1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':fac_first_name', $fac_first_name, PDO::PARAM_STR, strlen($fac_first_name));
    $stmt->bindParam(':fac_last_name', $fac_last_name, PDO::PARAM_STR, strlen($fac_last_name));
    $stmt->bindParam(':fac_national_id', $fac_national_id, PDO::PARAM_STR, strlen($fac_national_id));
    $stmt->bindParam(':fac_phone', $fac_phone, PDO::PARAM_STR, strlen($fac_phone));
    $stmt->bindParam(':person_first_name', $person_first_name, PDO::PARAM_STR, strlen($person_first_name));
    $stmt->bindParam(':person_last_name', $person_last_name, PDO::PARAM_STR, strlen($person_last_name));
    $stmt->bindParam(':person_national_id', $person_national_id, PDO::PARAM_STR, strlen($person_national_id));
    $stmt->bindParam(':person_phone', $person_phone, PDO::PARAM_STR, strlen($person_phone));
    $stmt->bindParam(':interaction_date', $interaction_date, PDO::PARAM_STR, strlen($interaction_date));
    $stmt->bindParam(':followup_date', $followup_date, PDO::PARAM_STR, strlen($followup_date));
    $stmt->bindParam(':type_id', $type_id, PDO::PARAM_STR, strlen($type_id));
    $stmt->bindParam(':note', $note, PDO::PARAM_STR, strlen($note));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertInteraction2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertInteraction exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}


function updateInteraction($timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $person_first_name, $person_last_name, $person_national_id, $person_phone, $interaction_date, $followup_date, $type_id, $note)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateInteraction0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($first_name, $last_name, $dob);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update interaction set
timestamp=:timestamp,
fac_first_name=:fac_first_name, 
fac_last_name=:fac_last_name,
fac_national_id=:fac_national_id,
fac_phone=:fac_phone,
person_first_name=:person_first_name,
person_last_name=:person_last_name,
person_national_id=:person_national_id,
person_phone=:person_phone,
interaction_date=:interaction_date, 
followup_date=:followup_date, 
type_id=:type_id, 
note=:note
where 1=1
and timestamp<:wtimestamp
and fac_first_name=:wfac_first_name
and fac_last_name=:wfac_last_name
and fac_phone=:wfac_phone
and person_first_name=:wperson_first_name
and person_last_name=:wperson_last_name
and person_phone=:wperson_phone
and interaction_date=:winteraction_date
-- and followup_date=:wfollowup_date
  ";
  
  file_put_contents('php_index_debug.log', 'updateInteraction1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    
    $stmt->bindParam(':fac_first_name', $fac_first_name, PDO::PARAM_STR, strlen($fac_first_name));
    $stmt->bindParam(':fac_last_name', $fac_last_name, PDO::PARAM_STR, strlen($fac_last_name));
    $stmt->bindParam(':fac_national_id', $fac_national_id, PDO::PARAM_STR, strlen($fac_national_id));
    $stmt->bindParam(':fac_phone', $fac_phone, PDO::PARAM_STR, strlen($fac_phone));
    
    $stmt->bindParam(':person_first_name', $person_first_name, PDO::PARAM_STR, strlen($person_first_name));
    $stmt->bindParam(':person_last_name', $person_last_name, PDO::PARAM_STR, strlen($person_last_name));
    $stmt->bindParam(':person_national_id', $person_national_id, PDO::PARAM_STR, strlen($person_national_id));
    $stmt->bindParam(':person_phone', $person_phone, PDO::PARAM_STR, strlen($person_phone));
    
    $stmt->bindParam(':interaction_date', $interaction_date, PDO::PARAM_STR, strlen($interaction_date));
    $stmt->bindParam(':followup_date', $followup_date, PDO::PARAM_STR, strlen($followup_date));
    $stmt->bindParam(':type_id', $type_id, PDO::PARAM_STR, strlen($type_id));
    $stmt->bindParam(':note', $note, PDO::PARAM_STR, strlen($note));
    
    $stmt->bindParam(':wtimestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    
    $stmt->bindParam(':wfac_first_name', $fac_first_name, PDO::PARAM_STR, strlen($fac_first_name));
    $stmt->bindParam(':wfac_last_name', $fac_last_name, PDO::PARAM_STR, strlen($fac_last_name));
    $stmt->bindParam(':wfac_phone', $fac_phone, PDO::PARAM_STR, strlen($fac_phone));
    
    $stmt->bindParam(':wperson_first_name', $person_first_name, PDO::PARAM_STR, strlen($person_first_name));
    $stmt->bindParam(':wperson_last_name', $person_last_name, PDO::PARAM_STR, strlen($person_last_name));
    $stmt->bindParam(':wperson_phone', $person_phone, PDO::PARAM_STR, strlen($person_phone));
    
    $stmt->bindParam(':winteraction_date', $interaction_date, PDO::PARAM_STR, strlen($interaction_date));
    // $stmt->bindParam(':wfollowup_date', $followup_date, PDO::PARAM_STR, strlen($followup_date));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateInteraction2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateInteraction exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function selectGroupActivity($id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'selectGroupActivity1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
id,
name,
timestamp,
location_id,
activity_date,
group_type_id,
institution_id,
males,
females,
messages,
latitude,
longitude
from group_activity
where 1=1 
and name = :name
and activity_date = :activity_date
  ";
  
  file_put_contents('php_index_debug.log', 'selectGroupActivity2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':name', $name, PDO::PARAM_STR, strlen($name));
    $stmt->bindParam(':activity_date', $activity_date, PDO::PARAM_STR, strlen($activity_date));
    
    file_put_contents('php_index_debug.log', 'selectGroupActivity3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectGroupActivity exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectGroupActivity3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function insertGroupActivity($id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertGroupActivity0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($name, $activity_date);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into group_activity (name, timestamp, location_id, activity_date, group_type_id, institution_id, males, females, messages, latitude, longitude)
values (:name, :timestamp, :location_id, :activity_date, :group_type_id, :institution_id, :males, :females, :messages, :latitude, :longitude)
  ";
  
  file_put_contents('php_index_debug.log', 'insertGroupActivity1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR, strlen($name));
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':activity_date', $activity_date, PDO::PARAM_STR, strlen($activity_date));
    $stmt->bindParam(':group_type_id', $group_type_id, PDO::PARAM_STR, strlen($group_type_id));
    $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR, strlen($institution_id));
    $stmt->bindParam(':males', $males, PDO::PARAM_STR, strlen($males));
    $stmt->bindParam(':females', $females, PDO::PARAM_STR, strlen($females));
    $stmt->bindParam(':messages', $messages, PDO::PARAM_STR, strlen($messages));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertGroupActivity2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertGroupActivity exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}


function updateGroupActivity($id, $name, $timestamp, $location_id, $activity_date, $group_type_id, $institution_id, $males, $females, $messages, $latitude, $longitude)
{
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateGroupActivity0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($name, $activity_date);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update group_activity set
name=:name,
timestamp=:timestamp,
location_id=:location_id,
activity_date=:activity_date,
group_type_id=:group_type_id,
institution_id=:institution_id,
males=:males,
females=:females,
messages=:messages,
latitude=:latitude,
longitude=:longitude
where 1=1
and name=:wname
and activity_date=:wactivity_date
  ";
  
  file_put_contents('php_index_debug.log', 'updateGroupActivity1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':name', $name, PDO::PARAM_STR, strlen($name));
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':activity_date', $activity_date, PDO::PARAM_STR, strlen($activity_date));
    $stmt->bindParam(':group_type_id', $group_type_id, PDO::PARAM_STR, strlen($group_type_id));
    $stmt->bindParam(':institution_id', $institution_id, PDO::PARAM_STR, strlen($institution_id));
    $stmt->bindParam(':males', $males, PDO::PARAM_STR, strlen($males));
    $stmt->bindParam(':females', $females, PDO::PARAM_STR, strlen($females));
    $stmt->bindParam(':messages', $messages, PDO::PARAM_STR, strlen($messages));
    $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
    $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
    $stmt->bindParam(':wname', $name, PDO::PARAM_STR, strlen($name));
    $stmt->bindParam(':wactivity_date', $activity_date, PDO::PARAM_STR, strlen($activity_date));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateGroupActivity2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateGroupActivity exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}

function selectUser($id, $timestamp, $username, $password, $email, $first_name, $last_name, $national_id, $phone, $region_id, $user_type_id, $location_id, $modified_by, $created_by, $is_blocked, $timestamp_updated, $timestamp_created, $timestamp_last_login)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'selectUser1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("params=", $timestamp, $fac_first_name, $fac_last_name, $fac_national_id, $fac_phone, $name, $activity_date, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $query = "
select
id,
timestamp,
username,
password,
email,
first_name,
last_name,
national_id,
phone,
region_id,
user_type_id,
location_id,
modified_by,
created_by,
is_blocked,
timestamp_updated,
timestamp_created,
timestamp_last_login
from user
where 1=1 
and username = :username
and phone = :phone
  ";
  
  file_put_contents('php_index_debug.log', 'selectUser2>' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("query=", $query, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    
    
    $stmt = $db->prepare($query);
    
    // $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    
    file_put_contents('php_index_debug.log', 'selectUser3a >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("queryString=", $stmt->queryString, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $result = $stmt->execute();
    $row    = $stmt->fetch();
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'selectUser exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    return null;
  }
  
  file_put_contents('php_index_debug.log', 'selectUser3b >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("row=", $row, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  return $row;
}

function insertUser($id, $timestamp, $username, $password, $email, $first_name, $last_name, $national_id, $phone, $region_id, $user_type_id, $location_id, $modified_by, $created_by, $is_blocked, $timestamp_updated, $timestamp_created, $timestamp_last_login)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'insertUser0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($name, $activity_date);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $insert = "
insert into user (timestamp, username, password, email, first_name, last_name, national_id, phone, region_id, user_type_id, location_id, modified_by, created_by, is_blocked, timestamp_updated, timestamp_created, timestamp_last_login)
values (:timestamp, :username, :password, :email, :first_name, :last_name, :national_id, :phone, :region_id, :user_type_id, :location_id, :modified_by, :created_by, :is_blocked, :timestamp_updated, :timestamp_created, :timestamp_last_login)
  ";
  
  file_put_contents('php_index_debug.log', 'insertUser1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("insert=", $insert, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($insert);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
    $stmt->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
    $stmt->bindParam(':email', $email, PDO::PARAM_STR, strlen($email));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':region_id', $region_id, PDO::PARAM_STR, strlen($region_id));
    $stmt->bindParam(':user_type_id', $user_type_id, PDO::PARAM_STR, strlen($user_type_id));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':modified_by', $modified_by, PDO::PARAM_STR, strlen($modified_by));
    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR, strlen($created_by));
    $stmt->bindParam(':is_blocked', $is_blocked, PDO::PARAM_STR, strlen($is_blocked));
    $stmt->bindParam(':timestamp_updated', $timestamp_updated, PDO::PARAM_STR, strlen($timestamp_updated));
    $stmt->bindParam(':timestamp_created', $timestamp_created, PDO::PARAM_STR, strlen($timestamp_created));
    $stmt->bindParam(':timestamp_last_login', $timestamp_last_login, PDO::PARAM_STR, strlen($timestamp_last_login));
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'insertUser2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("insert result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'insertUser exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}


function updateUser($id, $timestamp, $username, $password, $email, $first_name, $last_name, $national_id, $phone, $region_id, $user_type_id, $location_id, $modified_by, $created_by, $is_blocked, $timestamp_updated, $timestamp_created, $timestamp_last_login)
{
  
  global $db;
  
  file_put_contents('php_index_debug.log', 'updateUser0 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump($name, $activity_date);
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  $update = "
update user set
timestamp=:timestamp,
username=:username,
password=:password,
email=:email,
first_name=:first_name,
last_name=:last_name,
national_id=:national_id,
phone=:phone,
region_id=:region_id,
user_type_id=:user_type_id,
location_id=:location_id,
modified_by=:modified_by,
created_by=:created_by,
is_blocked=:is_blocked,
timestamp_updated=:timestamp_updated,
timestamp_created=:timestamp_created,
timestamp_last_login=:timestamp_last_login
where 1=1
and username=:wusername
and phone=:wphone
  ";
  
  file_put_contents('php_index_debug.log', 'updateUser1 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
  ob_start();
  var_dump("update=", $update, "END");
  $toss = ob_get_clean();
  file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
  
  try {
    $stmt = $db->prepare($update);
    
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR, strlen($timestamp));
    $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
    $stmt->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
    $stmt->bindParam(':email', $email, PDO::PARAM_STR, strlen($email));
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, strlen($first_name));
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, strlen($last_name));
    $stmt->bindParam(':national_id', $national_id, PDO::PARAM_STR, strlen($national_id));
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR, strlen($phone));
    $stmt->bindParam(':region_id', $region_id, PDO::PARAM_STR, strlen($region_id));
    $stmt->bindParam(':user_type_id', $user_type_id, PDO::PARAM_STR, strlen($user_type_id));
    $stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR, strlen($location_id));
    $stmt->bindParam(':modified_by', $modified_by, PDO::PARAM_STR, strlen($modified_by));
    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR, strlen($created_by));
    $stmt->bindParam(':is_blocked', $is_blocked, PDO::PARAM_STR, strlen($is_blocked));
    $stmt->bindParam(':timestamp_updated', $timestamp_updated, PDO::PARAM_STR, strlen($timestamp_updated));
    $stmt->bindParam(':timestamp_created', $timestamp_created, PDO::PARAM_STR, strlen($timestamp_created));
    $stmt->bindParam(':timestamp_last_login', $timestamp_last_login, PDO::PARAM_STR, strlen($timestamp_last_login));
    $stmt->bindParam(':wusername', $username, PDO::PARAM_STR, strlen($username));
    $stmt->bindParam(':wphone', $phone, PDO::PARAM_STR, strlen($phone));
    
    $result = $stmt->execute();
    
    file_put_contents('php_index_debug.log', 'updateUser2 >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("update result=", $result, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    
  }
  catch (PDOException $ex) {
    //die
    file_put_contents('php_index_debug.log', 'updateUser exception >' . PHP_EOL, FILE_APPEND | LOCK_EX);
    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean();
    file_put_contents('php_index_debug.log', $toss . PHP_EOL, FILE_APPEND | LOCK_EX);
    // continue?
  }
}



?>