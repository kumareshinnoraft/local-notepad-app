<?php

/**
 * This index is the starting point of the application and all routes started
 * from here. each submission calls a form submission which goes to the the file.
 */
require_once('./vendor/autoload.php');
require_once('./Service/Cookie.php');

// Cookie class contains if the user if active or not.
$cookie = new \App\Service\Cookie();

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// If user is active show user home screen instead login screen.
if ($cookie->isActive()) {
    echo $twig->render('home/index.html.twig');
} else {
    echo $twig->render('auth/login.html.twig');
}