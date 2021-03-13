<?php

use \Firebase\JWT\JWT;


class ComentarioController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function listarComentarios() {
    
      $eval = "SELECT * FROM comentario";
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

  public function crearComentario() {
    //Guardamos los parametros de la petición.
    $comentario = json_decode(file_get_contents("php://input"));

    //Comprobamos que los datos sean consistentes.
    if(!isset($comentario->idUsuario) || !isset($comentario->titulo) || !isset($comentario->texto) || !isset($comentario->nota) || !isset($comentario->idJuego)) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));

    }
    $eval = "INSERT INTO comentario (titulo,texto,nota,idJuego,idUsuario) VALUES (?,?,?,?,?)";
    $peticion = $this->db->prepare($eval);
    $peticion->execute([
      $comentario->titulo,$comentario->texto,$comentario->nota,$comentario->idJuego,$comentario->idUsuario
    ]);
      exit(json_encode("Comentario  creado"));
  }

  public function eliminarComentario($idComentario,$idUsuario) {
    if(IDUSER==$idUsuario) {
      $eval = "DELETE FROM comentario WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$idComentario]);
      http_response_code(200);
      exit(json_encode("comentario eliminado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "No puedes eliminar un mensaje que no es tuyo"]));            
    }
  }
}