<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type");

if (empty($_POST)) {
  $_POST = json_decode(file_get_contents("php://input"), true);
}

// $data = json_decode(file_get_contents("php://input"));

// Get database connection
include_once '../config/database.php';

// Instantiate user object
include_once '../objects/user.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Get posted data
$data = json_decode(json_encode($_POST), FALSE);

// Make sure data is not empty
if (
  !empty($data->mobile) &&
  !empty($data->firstName) &&
  !empty($data->lastName) &&
  !empty($data->email)
) {
  // Set product property values
  $user->mobile = $data->mobile;
  $user->firstName = $data->firstName;
  $user->lastName = $data->lastName;
  $user->dob = !empty($data->dob) ? $data->dob : null;
  $user->gender = !empty($data->gender) ? $data->gender : null;
  $user->email = $data->email;

  // Get existing user by Mobile
  $existing_mobile = $user->existing_user_by_mobile();
  $existing_email = $user->existing_user_by_email();

  if ($existing_mobile > 0 || $existing_email > 0) {
    // Set response code - 409 conflict
    http_response_code(200);

    die(json_encode(array(
      "message"         => "User already exists.",
      "isMobileExists" => $existing_mobile > 0,
      "isEmailExists" => $existing_email > 0
    )));
  }

  // Create the user
  if ($user->create()) {
    // Set response code - 201 created
    http_response_code(201);

    // Tell the user
    echo json_encode(array(
      "message"     => "User is successfully created.",
      "isSucceeded" => true
    ));

    exit();
  } else {
    // Set response code - 503 service unavailable
    http_response_code(503);

    // Tell the user
    die(json_encode(array("message" => "Unable to create user.")));
  }
}
// Tell the user data is incomplete
else {
  // Set response code - 400 bad request
  http_response_code(400);

  // Tell the user
  die(json_encode(array("message" => "Unable to create user. Required data is incomplete.")));
}
?>

<?php
exit();
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');

date_default_timezone_set("Asia/Jakarta");

$conn = new mysqli('localhost', 'root', 'root', 'registration_db');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (empty($_POST)) {
  $_POST = json_decode(file_get_contents("php://input"), true);
}

if ((!isset($_GET['action']) || empty($_GET['action'])) && (!isset($_POST['action']) || empty($_POST['action']))) {
  die(json_encode(array('errmsg' => 'Error: Undefined action.')));
}

if (!empty($_GET['action'])) {
  $action = $_GET['action'];
} else if (!empty($_POST['action'])) {
  $action = $_POST['action'];
}

switch ($action) {
  case '': {
      break;
    }
  default: {
      break;
    }
}

function cleanvalue($value)
{
  if (!isset($value)) {
    die(json_encode(array("errmsg" => "Parameter of cleanvalue is undefined.")));
  }

  // $value = htmlspecialchars($value);
  $value = trim($value);
  $value = str_replace("\\", "\\\\", $value);
  $value = str_replace("'", "\'", $value);
  $value = str_replace("\"", "\\\"", $value);

  return $value;
}
?>