<?php

class Usuario {
    public $email;
    private $password;
    public $id;
    public $foto;
    public $usuario;
    public $rol;
       
    
    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function getId() {
        return $this->id;
    }

    function getFoto() {
        return $this->foto;
    }

    function getUsuario() {
        return $this->usuario;
    }


    function setEmail($email): void {
        $this->email = $email;
    }

    function setPassword($password): void {
        $this->password = $password;
    }


    function setFoto($foto): void {
        $this->foto = $foto;
    }

    function setUsuario($usuario): void {
        $this->usuario = $usuario;
    }

    function setUid($uid): void {
        $this->uid = $uid;
    }
    function getRol() {
        return $this->rol;
    }

    function setRol($rol): void {
        $this->rol = $rol;
    }



}
