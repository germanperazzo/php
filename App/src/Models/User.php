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

    public function createUser($nombre_usuario, $clave, $es_admin)
    {   
        
        $stmt = $this->db->prepare("INSERT INTO usuario (nombre_usuario, clave, es_admin) VALUES (?, ?, ?)");
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

    public function updateToken($id, $token, $vencimiento)
    {
        $stmt = $this->db->prepare("UPDATE usuario SET token = ?, vencimiento_token = ? WHERE id = ?");
        $stmt->bind_param('ssi', $token, $vencimiento, $id);
        return $stmt->execute();
    }
    

    public function isTokenValid($token)
    {
        $query = "SELECT id FROM usuario WHERE token = ? AND vencimiento_token > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function getUserByUsuario($nombre_usuario)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE nombre_usuario = ?");
        $stmt->bind_param('i', $nombre_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }

}