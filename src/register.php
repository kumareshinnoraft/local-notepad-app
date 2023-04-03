<?php

/**
 * This file is used for registering user in the database.
 */
use App\Service\Cookie;
use App\Service\PerformedOperations;
use App\Service\SendEmail;

require_once('../Service/PerformOperations.php');
require_once('../Service/SendEmail.php');
require_once('../vendor/autoload.php');
require_once('../Service/Cookie.php');
include('DatabaseConnection.php');
require_once('../index.php');

// Cookie class contains if the user if active or not.
$cookie = new Cookie();

if ($cookie->isActive()) {
  echo $twig->render('home/index.html.twig');
}

// Storing all incoming values in variables
$fullName = $_GET('fullName');
$email    = $_GET('email');
$password = $_GET('password');
$gender   = $_GET('gender');
$image    = $_GET('file');
$userName = substr($email, 0, strrpos($email, '@'));

// Necessary operations to be performed.
$performOperation = new PerformedOperations();
$sendMail = new SendEmail();

// Generating a random otp.
$otp = $performOperation->generateOtp();
$msg = "Your one time password is ";

if ($id !== NULL) {

  // Sending mail to the receiver.
  $sendMail->sendEmail($email, $otp, $msg);

  // This block of code insert user in the database.
  $query = "INSERT INTO notes (email, user_name, full_name, image_name, gender, password, otp, otp_creation_time, account_creation_time, verified, is_active, account_creation_time) VALUES ('$email', '$userName', '$fullName', '$image', '$gender', '$password', '$otp', new DateTime, FALSE, TRUE, new DateTime)";

  // Executing the query.
  $flag = $conn->query($sql);

  // $flag returns boolean, if it is TRUE, it means user is created successfully.
  if ($flag) {

    // Setting cookie as user is active in the browser.
    $cookie->setCookie($email);
    echo "User created successfully";
  }

} else {
  echo "User not found";
}