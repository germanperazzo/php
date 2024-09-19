<?php

namespace App\Models;

class Game
{
    private $db;

    public function __construct()
    {
        $this->db = new \mysqli('localhost', 'root', '', 'seminariophp');
        if ($this->db->connect_error) {
            die("Error en la conexión: " . $this->db->connect_error);
        }
    }

    public function createGame($nombre, $descripcion, $imagen, $clasificacion_edad)
    {
        $stmt = $this->db->prepare("INSERT INTO juego (nombre, descripcion, imagen, clasificacion_edad) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nombre, $descripcion, $imagen, $clasificacion_edad);
        return $stmt->execute();
    }

    public function getGameById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM juego WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getGames($limit, $offset, $clasificacion = null, $texto = null, $plataforma = null)
    {
        $query = "SELECT * FROM juego WHERE 1=1";
        
        if ($clasificacion) {
            $query .= " AND clasificacion_edad = '$clasificacion'";
        }
        
        if ($texto) {
            $query .= " AND nombre LIKE '%$texto%'";
        }
        
        $query .= " LIMIT $limit OFFSET $offset";
        
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteGame($id)
    {
        $stmt = $this->db->prepare("DELETE FROM juego WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}