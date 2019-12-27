<?php
class User
{
  // Database connection and table name
  private $conn;
  private $table_name = "users";

  // Object properties
  public $id;
  public $mobile;
  public $firstName;
  public $lastName;
  public $dob;
  public $gender;
  public $email;

  // Constructor with $db as database connection
  public function __construct($db)
  {
    $this->conn = $db;
  }

  // Check mobile existance
  public function existing_user_by_mobile()
  {
    // Query to select record
    $query = "SELECT count(*) FROM " . $this->table_name . " WHERE mobile=:mobile";
    
    // Prepare query
    $stmt = $this->conn->prepare($query);

    // Sanitize
    $this->mobile = trim(htmlspecialchars(strip_tags($this->mobile)));

    $stmt->bindParam(":mobile", $this->mobile);

    // Execute query
    if ($stmt->execute()) {
      return $stmt->fetchColumn();
    }
    else {
      // Set response code - 503 service unavailable
      http_response_code(503);

      // Tell the user
      die(json_encode(array("message" => "Unable to check mobile number.")));
    }
  }

  // Check mobile existance
  public function existing_user_by_email()
  {
    // Query to select record
    $query = "SELECT count(*) FROM " . $this->table_name . " WHERE email=:email";
    
    // Prepare query
    $stmt = $this->conn->prepare($query);

    // Sanitize
    $this->email = trim(htmlspecialchars(strip_tags($this->email)));

    $stmt->bindParam(":email", $this->email);

    // Execute query
    if ($stmt->execute()) {
      return $stmt->fetchColumn();
    }
    else {
      // Set response code - 503 service unavailable
      http_response_code(503);

      // Tell the user
      die(json_encode(array("message" => "Unable to check email.")));
    }
  }

  // Create user
  public function create()
  {
    // Query to insert record
    $query = "INSERT INTO " . $this->table_name . " SET mobile=:mobile, firstName=:firstName, lastName=:lastName, dob=:dob, gender=:gender, email=:email";

    // Prepare query
    $stmt = $this->conn->prepare($query);

    // Sanitize
    $this->mobile = trim(htmlspecialchars(strip_tags($this->mobile)));
    $this->firstName = ucwords(trim(htmlspecialchars(strip_tags($this->firstName))));
    $this->lastName = ucwords(trim(htmlspecialchars(strip_tags($this->lastName))));
    $this->dob = !empty($this->dob) ? trim(htmlspecialchars(strip_tags($this->dob))) : null;
    $this->gender = !empty($this->gender) ? strtolower(trim(htmlspecialchars(strip_tags($this->gender)))) : null;
    $this->email = strtolower(trim(htmlspecialchars(strip_tags($this->email))));

    // Bind values
    $stmt->bindParam(":mobile", $this->mobile);
    $stmt->bindParam(":firstName", $this->firstName);
    $stmt->bindParam(":lastName", $this->lastName);
    $stmt->bindValue(":dob", !empty($this->dob) ? $this->dob : null, PDO::PARAM_STR);
    $stmt->bindValue(":gender", !empty($this->gender) ? $this->gender : null, PDO::PARAM_STR);
    $stmt->bindParam(":email", $this->email);

    // Execute query
    if ($stmt->execute()) {
      return true;
    }

    return false;
  }
}
