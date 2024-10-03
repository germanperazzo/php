<?php

namespace App\Models;

class User{
    private $db;

    public function __construct()
    {
        $this->db = new \mysqli('localhost', 'root', '', 'seminariophp');
        if ($this->db->connect_error) {
            die("Error en la conexión: " . $this->db->connect_error);
        }
    }

    public function registerUser($nombre_usuario, $clave){
        $stmt = $this->db->prepare("INSERT INTO usuario (nombre_usuario = ?, clave = ?)");
        $stmt->bind_param('ss', $nombre_usuario, $clave);
        return $stmt->execute();
    }

    public function createUser($nombre_usuario, $clave, $es_admin)
    {   
        
        $stmt = $this->db->prepare("INSERT INTO usuario (nombre_usuario = ?, clave = ?, es_admin = ?)");
        $stmt->bind_param('ssi', $nombre_usuario, $clave, $es_admin);
        return $stmt->execute();
    }

    public function updateUser($id, $nombre_usuario, $clave , ){
       
        $stmt = $this->db->prepare("UPDATE usuario SET nombre_usuario = ?, clave = ?  WHERE id = ?");
    
        $stmt->bind_param('ssi', $nombre_usuario, $clave,  $id );
    
        
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuario WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateToken($nombre_usuario, $token, $vencimiento)
    {
        $stmt = $this->db->prepare("UPDATE usuario SET token = ?, vencimiento_token = ? WHERE nombre_usuario = ?");
        $stmt->bind_param('sss', $token, $vencimiento, $nombre_usuario);
        return $stmt->execute();
    }
    

    public function getToken($id)
    {
        ;
        $stmt = $this->db->prepare("SELECT  token, vencimiento_token FROM usuario WHERE id = ? ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        

        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserByUsuario($nombre_usuario)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE nombre_usuario = ?");
        $stmt->bind_param('s', $nombre_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }


    public function getUserCalificaciones($id)
    {
        $stmt = $this->db->prepare(
            "SELECT , u.nombre_usuario, c.estrellas, j.nombre AS juego_nombre FROM usuario u
            JOIN calificacion c ON u.id = c.usuario_id
            JOIN juego j ON c.juego_id = j.id
            WHERE u.id = ?");
        $stmt->bind_param('i', $nombre_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }

}