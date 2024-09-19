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

    public function updateUser($id, $nombre_usuario = null, $clave = null, $token = null, $vencimiento_token = null, $es_admin = null){
        // Comenzar con la consulta base
        $sql = "UPDATE usuario SET ";
        $params = [];
        $types = '';
    
        // Crear un array de campos y valores a actualizar
        if ($nombre_usuario !== null) {
            $sql .= "nombre_usuario = ?, ";
            $params[] = $nombre_usuario;
            $types .= 's'; // String para nombre_usuario
        }
        if ($clave !== null) {
            $sql .= "clave = ?, ";
            $params[] = $clave;
            $types .= 's'; // String para clave
        }
        if ($token !== null) {
            $sql .= "token = ?, ";
            $params[] = $token;
            $types .= 's'; // String para token
        }
        if ($vencimiento_token !== null) {
            $sql .= "vencimiento_token = ?, ";
            $params[] = $vencimiento_token;
            $types .= 's'; // String para vencimiento_token
        }
        if ($es_admin !== null) {
            $sql .= "es_admin = ?, ";
            $params[] = $es_admin;
            $types .= 'i'; // Integer para es_admin
        }
    
        // Remover la última coma y agregar la condición WHERE con id
        $sql = rtrim($sql, ', ') . " WHERE id = ?";
        $params[] = $id; // Agregar el id del usuario como último parámetro
        $types .= 'i'; // Integer para id
    
        // Preparar la consulta
        $stmt = $this->db->prepare($sql);
    
        // Vincular los parámetros
        $stmt->bind_param($types, ...$params);
    
        // Ejecutar la consulta
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
    
    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuario WHERE id = ?");
        $stmt->bind_param('i', $id);
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

}