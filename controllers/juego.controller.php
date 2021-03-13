<?php

use \Firebase\JWT\JWT;


class JuegoController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function listarJuegos() {
    
      $eval = "SELECT * FROM juego";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    
  }

  public function verJuego($id) {
      $consulta = "SELECT * FROM juego WHERE id=?";
      $peticion = $this->db->prepare($consulta);
      $peticion->execute([$id]);
      $resultado = $peticion->fetchObject();
      if(empty($resultado)){
          exit(json_encode(["error" => "No se encuentra el juego"]));
      }else{
          exit(json_encode($resultado));
      }
    }

  public function crearJuego() {
    //Guardamos los parametros de la petición.
    $juego = json_decode(file_get_contents("php://input"));

    //Comprobamos que los datos sean consistentes.
    if(!isset($juego->nombre) || !isset($juego->fechaDeLanzamiento) || !isset($juego->comprar) || !isset($juego->edad) || !isset($juego->creador) || !isset($juego->genero) || !isset($juego->numeroDeJugadores) || !isset($juego->fechaDePublicacion) || !isset($juego->imagen) || !isset($juego->nota) || !isset($juego->resumen) ) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));

    }
    $eval = "INSERT INTO juego (nombre,fechaDeLanzamiento,comprar,edad,creador,genero,numeroDeJugadores,imagen,nota,resumen) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $peticion = $this->db->prepare($eval);
    $peticion->execute([
      $juego->nombre,$juego->fechaDeLanzamiento,$juego->comprar,$juego->edad,$juego->creador,$juego->genero,$juego->numeroDeJugadores,$juego->imagen,$juego->nota,$juego->resumen
    ]);
      exit(json_encode("Juego creado"));
  }

  public function eliminarJuego($id) {
    if(IDUSER) {
      $eval = "DELETE FROM juego WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$id]);
      http_response_code(200);
      exit(json_encode("juego eliminado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
}