<?php 
use App\Service\Cookie;

require_once('../Service/Cookie.php');
require_once('DatabaseConnection.php');

$cookie = new Cookie();

// Getting content and title from the form submission.
$content = $_GET['content'];
$title = $_GET['title'];

// Getting cookie from the COOKIES.
$email = $cookie->getCookie("email");

// Getting id of the user from the email ID which is stored in the cookie.
$sql1 = "SELECT id FROM user WHERE email = '$email'";
$id = $conn->query($sql);

// Storing all data in the database.
$sql2 = "INSERT INTO notes (author_id, content, created_time, title) VALUES ('$id', '$content', new DateTime, $title)";
if($conn->query($sql2)) {
  echo "Data stored successfully";
}

// Fetching all notes from the user;
$sql3 = "SELECT * FROM notes WHERE author_id = $id";
$notes = $conn->query($sql3);

// Showing different notes from the database.
$notesList = [];
foreach ($notes as $note) {
  echo "
  <div onclick='showNote($note->getId())'>
    <h4>$note->getTitle()</h4>
    <p>$note->getContent()</p>
    <span>$note->getCreatedTime()</span>
  </div>
  ";
}