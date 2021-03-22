<?php

use \Firebase\JWT\JWT;
include "PHPMailer/class.phpmailer.php";
include "PHPMailer/class.smtp.php";

class UserController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function listarUser() {
    //Comprueba si el usuario esta registrado.
    
      $eval = "SELECT * FROM usuario";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    
  }

  public function leerPerfil() {
 
      $consulta = "SELECT id,email,foto,usuario,password,rol FROM usuario WHERE id=?";
      $peticion = $this->db->prepare($consulta);
      $peticion->execute([IDUSER]);
      $resultado = $peticion->fetchObject();
      exit(json_encode($resultado));

      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));       
    
  }

  public function hacerLogin() {
    //Se obtienen los datos recibidos en la peticion.
    $user = json_decode(file_get_contents("php://input"));

    if(!isset($user->email) || !isset($user->password)) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));
    }
  
    //Primero busca si existe el usuario, si existe que obtener el id y la password.
    $peticion = $this->db->prepare("SELECT id,password FROM usuario WHERE email = ?");
    $peticion->execute([$user->email]);
    $resultado = $peticion->fetchObject();
  
    if($resultado) {
        
      if(password_verify($user->password, $resultado->password)) {
  
        //Preparamos el token.
        $iat = time();
        $exp = $iat + 3600*24*2;
        $token = array(
          "id" => $resultado->id,
          "iat" => $iat,
          "exp" => $exp
        );
  
        //Calculamos el token JWT y lo devolvemos.
        $jwt = JWT::encode($token, CJWT);
        http_response_code(200);
        exit(json_encode($jwt));
  
      } else {
        http_response_code(401);
        exit(json_encode(["error" => "Password incorrecta"]));
      }
  
    } else {
      http_response_code(404);
      exit(json_encode(["error" => "No existe el usuario"]));  
    }
  }

  public function subirAvatar() {
    if(is_null(IDUSER)){
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));
    }
    if(isset($_FILES['imagen'])) {
      $imagen = $_FILES['imagen'];
      $mime = $imagen['type'];
      $size = $imagen['size'];
      $rutaTemp = $imagen['tmp_name'];
  
      //Comprobamos que la imagen sea JPEG o PNG y que el tamaño sea menor que 400KB.
      if( !(strpos($mime, "jpeg") || strpos($mime, "png")) || ($size > 400000) ) {
        http_response_code(400);
        exit(json_encode(["error" => "La imagen tiene que ser JPG o PNG y no puede ocupar mas de 400KB"]));
      } else {
  
        //Comprueba cual es la extensión del archivo.
        $ext = strpos($mime, "jpeg") ? ".jpg":".png";
        $nombreFoto = "p-".IDUSER."-".time().$ext;
        $ruta = ROOT."images/".$nombreFoto;
  
        //Comprobamos que el usuario no tenga mas fotos de perfil subidas al servidor.
        //En caso de que exista una imagen anterior la elimina.
        $imgFind = ROOT."images/p-".IDUSER."-*";
        $imgFile = glob($imgFind);
        foreach($imgFile as $fichero) unlink($fichero);
        
        //Si se guarda la imagen correctamente actualiza la ruta en la tabla usuarios
        if(move_uploaded_file($rutaTemp,$ruta)) {
  
          //Prepara el contenido del campo imgSrc
          $imgSRC = "http://localhost/backendphp/images/".$nombreFoto;
  
          $eval = "UPDATE usuario SET foto=? WHERE id=?";
          $peticion = $this->db->prepare($eval);
          $peticion->execute([$imgSRC,IDUSER]);
  
          http_response_code(201);
          exit(json_encode("Imagen actualizada correctamente"));
        } else {
          http_response_code(500);
          exit(json_encode(["error" => "Ha habido un error con la subida"]));      
        }
      }
    }  else {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));
    }
  }

  public function registrarUser() {
    //Guardamos los parametros de la petición.
    $user = json_decode(file_get_contents("php://input"));

    //Comprobamos que los datos sean consistentes.
    if(!isset($user->email) || !isset($user->password) || !isset($user->usuario) ) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));

    }



    //Comprueba que no exista otro usuario con el mismo email.
    $peticion = $this->db->prepare("SELECT id FROM usuario WHERE email=?");
    $peticion->execute([$user->email]);
    $resultado = $peticion->fetchObject();
    if(!$resultado) {
      $password = password_hash($user->password, PASSWORD_BCRYPT); 
      $foto="no_foto";
      $rol="user";
      $eval = "INSERT INTO usuario (email,foto,usuario,password,rol) VALUES (?,?,?,?,?)";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([
        $user->email,$foto,$user->usuario,$password,$rol
      ]);
      
      //Preparamos el token.
      $id = $this->db->lastInsertId();
      $iat = time();
      $exp = $iat + 3600*24*2;
      $token = array(
        "id" => $id,
        "iat" => $iat,
        "exp" => $exp
      );

      //Calculamos el token JWT y lo devolvemos.
      $jwt = JWT::encode($token, CJWT);
      http_response_code(201);
      echo json_encode($jwt);
    } else {
      http_response_code(409);
      echo json_encode(["error" => "Ya existe este usuario"]);
    }
  }

  public function editarUser() {
    if(IDUSER) {
      //Cogemos los valores de la peticion.
      $user = json_decode(file_get_contents("php://input"));
      
      //Comprobamos si existe otro usuario con ese correo electronico.
      if(isset($user->email)) {
        $peticion = $this->db->prepare("SELECT id FROM usuario WHERE email=?");
        $peticion->execute([$user->email]);
        $resultado = $peticion->fetchObject();
        
        //Comprobamos si hay algun resultado, sino continuamos editando.
        if($resultado) {
          //Si el id del usuario con este email es distinto del usuario que ha hecho LOGIN.
          if($resultado->id != IDUSER) {
            http_response_code(409);
            exit(json_encode(["error" => "Ya existe un usuario con este email"]));              
          }
        } 
      }

      //Obtenemos los datos guardados en el servidor relacionados con el usuario
      $peticion = $this->db->prepare("SELECT email,foto,usuario,password,rol FROM usuario WHERE id=?");
      $peticion->execute([IDUSER]);
      $resultado = $peticion->fetchObject();

      //Combinamos los datos de la petición y de los que había en la base de datos.
      $nUsuario = isset($user->usuario) ? $user->usuario : $resultado->usuario;
      $nEmail = isset($user->email) ? $user->email : $resultado->email;

      //Si hemos recibido el dato de modificar la password.
      if(isset($user->password) && (strlen($user->password))){

        //Encriptamos la contraseña.
        $nPassword = password_hash($user->password, PASSWORD_BCRYPT);
        //Preparamos la petición.
        $eval = "UPDATE usuario SET usuario=?,email=?,password=? WHERE id=?";
        $peticion = $this->db->prepare($eval);
        $peticion->execute([$nUsuario,$nPassword,$nEmail,IDUSER]);
      } else {
        $eval = "UPDATE usuario SET usuario=?,email=? WHERE id=?";
        $peticion = $this->db->prepare($eval);
        $peticion->execute([$nUsuario,$nEmail,IDUSER]);        
      }
      http_response_code(201);
      exit(json_encode("Usuario actualizado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));         
    }
  }

  public function eliminarUser() {
    if(IDUSER) {
        
      //Buscamos si el usuario tenía imagenes y la eliminamos.
      $imgSrc = ROOT."images/p-".IDUSER."-*";
      $imgFile = glob($imgSrc);
      foreach($imgFile as $fichero) unlink($fichero);
      $eval = "DELETE FROM usuario WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([IDUSER]);
      http_response_code(200);
      exit(json_encode("Usuario eliminado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
  
  public function recuperarPassword() {
    //Se obtienen los datos recibidos en la peticion.
    $user = json_decode(file_get_contents("php://input"));

    if(!isset($user->email) || !isset($user->usuario)) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));
    }
  
    //Primero busca si existe el usuario, si existe que obtener el id y la password.
    $peticion = $this->db->prepare("SELECT password FROM usuario WHERE email = ? AND usuario = ?");
    $peticion->execute([$user->email,$user->usuario]);
    $resultado = $peticion->fetchObject();
  
    if($resultado) {
        $password=$resultado->password ;
      
        // Store the cipher method 
        $ciphering = "AES-128-CTR"; 

        $options = 0; 

        // Non-NULL Initialization Vector for decryption 
        $decryption_iv = '1234567891011121'; 

        // Store the decryption key 
        $decryption_key = "&Vf3oYx!oX8QQRKyZ@eV"; 

        // Use openssl_decrypt() function to decrypt the data 
        $password=openssl_decrypt ($password, $ciphering, 
                        $decryption_key, $options, $decryption_iv); 
        
        $destinatario = $user->email;
        $asunto = "Recuperar Contraseña";
        $mensaje="La contraseña de su cuenta es " . $password;
        
        
        $email_user = "manuelproyecto484@gmail.com"; //Mi correo
        $email_password = "2FJVx7PRpy2zjXe"; //Pass de mi correo
        $the_subject = $asunto;
        $address_to = $destinatario;
        $from_name = "Prueba";
        $phpmailer = new PHPMailer();
        // ---------- datos de la cuenta de Gmail ---------------------
        $phpmailer->Username = $email_user;
        $phpmailer->Password = $email_password; 
        //-----------------------------------------------------------------------
        // $phpmailer->SMTPDebug = 1;
        $phpmailer->SMTPSecure = 'ssl';
        $phpmailer->Host = "smtp.gmail.com"; // GMail
        $phpmailer->Port = 465;
        $phpmailer->IsSMTP(); // use SMTP
        $phpmailer->SMTPAuth = true;
        $phpmailer->setFrom($phpmailer->Username,$from_name);
        $phpmailer->AddAddress($address_to); // recipients email
        $phpmailer->Subject = $the_subject; 
        $phpmailer->Body = $mensaje;
        $phpmailer->IsHTML(true);
        $phpmailer->Send();
        
        exit(json_encode(["Mensaje enviado"]));
    }
  }
  
  public function editarPassword() {
    if(IDUSER) {
      //Cogemos los valores de la peticion.
      $user = json_decode(file_get_contents("php://input"));
      
      if(!isset($user->passwordAntigua) || !isset($user->passwordNueva)) {
      http_response_code(400);
      exit(json_encode($user));
    }

      //Obtenemos los datos guardados en el servidor relacionados con el usuario
      $peticion = $this->db->prepare("SELECT email,foto,usuario,password,rol FROM usuario WHERE id=?");
      $peticion->execute([IDUSER]);
      $usuarioBaseDeDatos = $peticion->fetchObject();
      
      if(password_verify($user->passwordAntigua, $usuarioBaseDeDatos->password)){
          $nPassword = password_hash($user->passwordNueva, PASSWORD_BCRYPT);
          $eval = "UPDATE usuario SET password=? WHERE id=?";
          $peticion = $this->db->prepare($eval);
          $peticion->execute([$nPassword,IDUSER]);
          http_response_code(201);
          exit(json_encode("Usuario actualizado correctamente"));
      }
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));         
    }
  }
}