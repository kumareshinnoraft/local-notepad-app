<?php
use App\Service\Cookie;

require_once('../Service/PerformOperations.php');
require_once('../vendor/autoload.php');
require_once('../Service/Cookie.php');
include('DatabaseConnection.php');

$cookie = new Cookie();

if ($cookie->isActive()) {
  echo $twig->render('home/index.html.twig');
}

// Getting cookie from the COOKIES.
$email = $cookie->getCookie("email");

// Storing all incoming values in variables
$otp = $_GET('1') . $_GET('2') . $_GET('3') . $_GET('4');

$dbOTP = $conn->query("SELECT otp FROM user WHERE email = '$email'");

// Checking the OTP sent by the server and entered by the user is same or not.
if ($otp === $dbOTP) {
  // Set user as verified.
  $query = "INSERT INTO user (is_verified) VALUES (TRUE)";
  $conn->query($query);
} else {
  echo "OTP does not matched";
}
