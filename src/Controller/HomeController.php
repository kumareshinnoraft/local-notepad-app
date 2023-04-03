<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PerformedOperations;
use App\Service\Cryptography;
use App\Service\Cookie;
use App\Entity\Notes;
use App\Entity\User;
use DateTime;

/**
 * This Controller is responsible for managing the home screen of the user.
 *
 * @package Doctrine
 * @subpackage ORM
 * 
 * @author Kumaresh Baksi <kumaresh.baksi@innoraft.com>
 * @version 1.0
 * @license INNORAFT
 */
class HomeController extends AbstractController
{
  public const ERROR = "Something went wrong";
  /**
   * This object is used to store and retrieve cookie.
   *
   * @var object
   */
  private $cookie;
  /**
   * This user object is used to point user entity class.
   *
   * @var object
   */
  private $user;
  /**
   * This note object is used to point notes entity class.
   *
   * @var object
   */
  private $note;
  /**
   * Cryptography object encode and decode values before
   * sending in link or storing password.
   *
   * @var object
   */
  private $cryptography;
  /**
   * This object provides different functions for user operations.
   *
   * @var object
   */
  private $performOperation;
  /**
   * This user table object is used to point user database tables.
   *
   * @var object
   */
  private $userTable;
  /**
   * This notes table object is used to point notes database tables.
   *
   * @var object
   */
  private $notesTable;
  /**
   * Entity Manager class object that manages the persistence and
   * retrieval of entity objects from the database.
   *
   * @var object
   */
  private $em;
  /**
   * This constructor is initializing the objects and also provides access of
   * entity manager interface.
   *
   * @param object $em
   *   EntityManagerInterface is used to manage entity with database
   *   it helps to alter database easily.
   *
   * @return void
   *   Contractor does not return anything instead it is used to initialize
   *   the object.
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->userTable        = $em->getRepository(User::class);
    $this->notesTable       = $em->getRepository(Notes::class);
    $this->performOperation = new PerformedOperations();
    $this->cryptography     = new Cryptography();
    $this->cookie           = new Cookie();
    $this->note             = new Notes();
    $this->user             = new User();
    $this->em               = $em;
  }
  /**
   * Home controller is the main feed where all user notes will be shown.
   *
   * @Route("/home", name="home")
   *   This route is for sending user to the home screen.
   *
   * @param object $request
   *   Request object handles parameter from query parameter.
   *
   * @return Response
   *   Response the view which contains user stored information.
   */
  public function index(Request $request): Response
  {
    if (!$this->cookie->isActive($request)) {
      return $this->redirectToRoute('loginUser');
    }
    return $this->render('home/index.html.twig');
  }

  /**
   * This root redirects user to home page, home pages if the user is already
   * logged in or not if not then user will be redirected to login page.
   *
   * @Route("/", name="root")
   *   This route is for sending user to the home screen.
   *
   * @return Response
   *   This response will be to the home screen.
   */
  public function rootPage(): Response
  {
    return $this->redirectToRoute('home');
  }

  /**
   * This routes stores the note in the database.
   *
   * @Route("/storeNote", name="storeNote")
   *   This route is for storing new notes in the database.
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.
   * 
   * @return Response
   *   This response will be to the home screen.
   */
  public function storeNote(Request $request): Response
  {
    // Getting the comment and post id from the query parameters.
    $noteContent  = $request->request->get('text');
    $titleContent = $request->request->get('title');
    $user         = $this->userTable->findOneBy(["email" => $this->cookie->getCookie('email', $request)]);

    if ($noteContent !== NULL) {

      // If the note content is present.
      $this->note->setContent($this->performOperation->sanitizeData($noteContent));
      $this->note->setTitle($this->performOperation->sanitizeData($titleContent));
      $this->note->setCreatedTime(new DateTime());
      $this->note->setAuthor($user);

      $this->em->persist($this->note);
      $this->em->flush();

      $note = $this->notesTable->find($this->note->getId());

      return new JsonResponse($this->performOperation->extractSingleNote($note));
    }
    return new JsonResponse(HomeController::ERROR);
  }

  /**
   * This root redirects user to home page, home pages if the user is already
   * logged in or not if not then user will be redirected to login page.
   *
   * @Route("/fetchAllNotes", name="fetchAllNotes")
   *   This route is for sending user to the home screen.
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.
   * 
   * @return Response
   *   This response will be to the home screen.
   */
  public function fetchAllNotes(Request $request): Response
  {
    // Getting the comment and post id from the query parameters.
    $user = $this->userTable->findOneBy(["email" => $this->cookie->getCookie('email', $request)]);

    if ($user !== NULL) {
      // Fetching all notes of a user.
      return new JsonResponse($this->performOperation->extractNotes($user->getNotes()));
    }
    return new JsonResponse(HomeController::ERROR);
  }

  /**
   * This root redirects user to home page, home pages if the user is already
   * logged in or not if not then user will be redirected to login page.
   *
   * @Route("/fetchSingeNote", name="fetchSingeNote")
   *   This route is for sending user to the home screen.
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.
   * 
   * @return Response
   *   This response will be to the home screen.
   */
  public function fetchSingeNote(Request $request): Response
  {

    $note = $this->notesTable->find($request->request->get("id"));
    if ($note !== NULL) {
      // Getting the comment and post id from the query parameters.
      return new JsonResponse($this->cryptography->encode($note->getId()));
    }
    return new JsonResponse(HomeController::ERROR);
  }

  /**
   * This is the edit post screen where user can edit delete and update the 
   * notes.
   *
   * @Route("/editPost", name="editPost")
   *   This route is for editing screen for notes
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.  
   * 
   * @return Response
   *   This response will be to the edit screen.
   */
  public function editPost(Request $request): Response
  {
    $noteId = $request->get("postId");
    $note = $this->notesTable->find($this->cryptography->decode($noteId));

    return $this->render('home/edit.html.twig', [
      "title"   => $note->getTitle(),
      "id"      => $note->getId(),
      "content" => $note->getContent(),
    ]);

  }

  /**
   * This page updated the note in the database.
   *
   * @Route("/updateNote", name="updateNote")
   *   Updated the notes in the database.
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.
   * 
   * @return Response
   *   This Json response is sent to the ajax call.
   */
  public function updateNote(Request $request): Response
  {
    // Getting the comment and post id from the query parameters.
    $noteContent  = $request->request->get('text');
    $titleContent = $request->request->get('title');
    $id           = $request->request->get('id');

    $user = $this->userTable->findOneBy(["email" => $this->cookie->getCookie('email', $request)]);

    if ($id !== NULL) {

      // Updating the notes.
      $note = $this->notesTable->find($id);
      $note->setTitle($this->performOperation->sanitizeData($titleContent));
      $note->setContent($this->performOperation->sanitizeData($noteContent));
      $note->setUpdatedTime(new DateTime);
      $note->setAuthor($user);

      // 
      $this->em->persist($note);
      $this->em->flush();
      return new JsonResponse(TRUE);
    }
    return new JsonResponse(HomeController::ERROR);
  }

  /**
   * This Routes delete the Note from the database.
   *
   * @Route("/deleteNote", name="deleteNote")
   *   This route is for deleting the note.
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.
   * 
   * @return Response
   *   This response will be to the home screen.
   */
  public function deleteNote(Request $request): Response
  {
    // Getting the comment and post id from the query parameters.
    $id = $request->request->get('id');

    $user = $this->userTable->findOneBy(["email" => $this->cookie->getCookie('email', $request)]);
    $note = $this->notesTable->find($id);

    // First removing user from the note.
    $user->removeNote($note);
    $this->em->persist($note);
    $this->em->flush();

    // Removing note permanently from database.
    $this->em->remove($note);
    $this->em->flush();

    return new JsonResponse(TRUE);
  }
}