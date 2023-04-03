<?php
use App\Service\Cookie;

require_once('../Service/PerformOperations.php');
require_once('../vendor/autoload.php');
require_once('../Service/Cookie.php');
include('DatabaseConnection.php');

$cookie = new Cookie();

// Check if the user is active or not.
if ($cookie->isActive()) {
  echo $twig->render('home/index.html.twig');
}

$email = $_GET('email');
$password = $_GET('password');

$id = "SELECT id FROM user WHERE email = '$email'";

if ($id === NULL) {
  echo "User is not present";
} else {
  $dbPassword = "SELECT password FROM user WHERE email = '$email'";
  if($password === $dbPassword) {
    // Setting cookie as user is active in the browser.
    $cookie->setCookie($email);
    echo $twig->render('home/index.html.twig');
  }
}