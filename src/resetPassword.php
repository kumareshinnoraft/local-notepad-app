<?php

/**
 * This file is used for registering user in the database.
 */
use App\Service\PerformedOperations;
use App\Service\Cryptography;
use App\Service\SendEmail;
use App\Service\Cookie;

require_once('../Service/PerformOperations.php');
require_once('../Service/Cryptography.php');
require_once('../Service/SendEmail.php');
require_once('../vendor/autoload.php');
require_once('../Service/Cookie.php');
include('DatabaseConnection.php');
require_once('index.php');

// Cookie class contains if the user if active or not.
$cookie = new Cookie();

if ($cookie->isActive()) {
  echo $twig->render('home/index.html.twig');
}

// Getting both password and new password field.
$newPassword = $_GET('password1');
$password = $_GET('password2');


// Necessary operations to be performed.
$performOperation = new PerformedOperations();
$cryptography = new Cryptography();
$sendMail = new SendEmail();

// Generating a random otp.
// Id in the URL parameters.
$id = $request->get('id');

// Decoding ID and concatenating base64 encoded code.
$userId = $cryptography->decode($id . "%3D");

if ($userId !== NULL && $newPassword === $password) {

  // Sending mail to the receiver.
  $sendMail->sendEmail($email, $otp, $msg);

  // This block of code insert user in the database.
  $query = "INSERT INTO user (password) VALUES ('$password) where id='$id'";

  // Executing the query.
  $flag = $conn->query($sql);

  // $flag returns boolean, if it is TRUE, it means user is created successfully.
  if ($flag) {

    // Setting cookie as user is active in the browser.
    echo "User Password updated successfully";
  }
} else {
  echo "User not found";
}