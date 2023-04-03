<?php
use App\Service\Cookie;
require_once('../Service/Cookie.php');

// Creating new object for the cookie.
$cookie = new Cookie();

// Removing cookies and sessions from the browser.
$cookie->removeCookie();
