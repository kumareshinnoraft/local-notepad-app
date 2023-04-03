<?php 
use App\Service\Cookie;

require_once('../Service/Cookie.php');
include('DatabaseConnection.php');

$cookie = new Cookie();

// Getting content and title from the form submission.
$noteId = $_GET['noteId'];

// Getting cookie from the COOKIES.
$email = $cookie->getCookie("email");

// Getting id of the user from the email ID which is stored in the cookie.
$sql = "DELETE FROM notes WHERE id = '$noteId'";

if ($conn->query($sql)) {
  echo "Note deleted successfully";
}
