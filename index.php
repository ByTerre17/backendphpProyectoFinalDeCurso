<?php
//Importamos las librerias necesarias.
require_once 'config/db.php';
require_once 'config/cors.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

//Guardamos la url para buscar el controlador y ponemos mensaje de bienvenida.
if(!isset($_GET['url'])) {
  exit(json_encode(["Bienvenido al Backend con routes"]));
}

$url = $_GET['url'];

//Preparamos la conexion con la base de datos
$bd = new db();
$conexion = $bd->getConnection();

//Comprueba si hay algún token valido en la cabecera y obtiene el ID del USER
$idUser = null;
if(!empty($_SERVER['HTTP_AUTHORIZATION'])) {
  $jwt = $_SERVER['HTTP_AUTHORIZATION'];
  try {
    $JWTraw = JWT::decode($jwt, $bd->getClave(), array('HS256'));
    $idUser = $JWTraw->id;
  } catch (Exception $e) { }
}

//Guardamos las variables globales. IDUSER, Metodo, CJWT, DIRECTORIO ROOT.
define('IDUSER', $idUser);
define('METODO', $_SERVER["REQUEST_METHOD"]);
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CJWT', $bd->getClave());

//Procesamos la ruta y los metodos.
$control = explode('/',$url);
switch($control[0]) {

  case "user":
    require_once("controllers/user.controller.php");
    $controladorUsuarios = new UserController($conexion);
    switch(METODO) {
      case "GET":
        switch($control[1]) {
          case "list":
            $controladorUsuarios->listarUser();
            break;
          case "":
            $controladorUsuarios->leerPerfil();
            break;
        }
        break;

      case "POST":
        switch($control[1]) {
          case "login":
            $controladorUsuarios->hacerLogin();
            break;
          case "image":
            $controladorUsuarios->subirAvatar();
            break;
        case "recuperarPassword":
            $controladorUsuarios->recuperarPassword();
            break;
          case "":
            $controladorUsuarios->registrarUser();
        }
        break;

      case "PUT":
        $controladorUsuarios->editarUser();
        break;

      case "DELETE":
        $controladorUsuarios->eliminarUser();
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));  
    }  
    break;

  case "juego":
    require_once("controllers/juego.controller.php");
    $controladorJuegos = new JuegoController($conexion);
    switch(METODO) {
      case "GET":
        switch($control[1]) {
          case "list":
            $controladorJuegos->listarJuegos();
            break;
          case "ver":
            $controladorJuegos->verJuego($control[2]);
            break;
        }

      case "POST":
        $controladorJuegos->crearJuego();
        break;

//      case "PUT":
//        $controladorJuegos->editarJuego();
//        break;

      case "DELETE":
        $controladorJuegos->eliminarJuego($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    
    case "comentario":
    require_once("controllers/comentario.controller.php");
    $controladorComentarios = new ComentarioController($conexion);
    switch(METODO) {
      case "GET":
        switch($control[1]) {
          case "list":
            $controladorComentarios->listarComentarios();
            break;
        
          case "listPorJuego":
            $controladorComentarios->listarComentariosPorJuego($control[2]);
            break;
          case "ver":
            $controladorJuegos->verJuego($control[2]);
            break;
        }

      case "POST":
        $controladorComentarios->crearComentario();
        break;

//      case "PUT":
//        $controladorJuegos->editarJuego();
//        break;

      case "DELETE":
        $controladorComentarios->eliminarComentario($control[1],$control[2]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

    default:
    exit(json_encode(["Bienvenido al Backend con routes"]));
}

