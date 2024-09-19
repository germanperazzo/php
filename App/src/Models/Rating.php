<?php

namespace App\Models;

class Rating
{
    private $db;

    public function __construct()
    {
        $this->db = new \mysqli('localhost', 'root', '', 'seminariophp');
        if ($this->db->connect_error) {
            die("Error en la conexión: " . $this->db->connect_error);
        }
    }

    public function createRating($estrellas, $usuario_id, $juego_id)
    {
        $stmt = $this->db->prepare("INSERT INTO calificacion (estrellas, usuario_id, juego_id) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $estrellas, $usuario_id, $juego_id);
        return $stmt->execute();
    }

    public function updateRating($id, $estrellas)
    {
        $stmt = $this->db->prepare("UPDATE calificacion SET estrellas = ? WHERE id = ?");
        $stmt->bind_param('ii', $estrellas, $id);
        return $stmt->execute();
    }

    public function deleteRating($id)
    {
        $stmt = $this->db->prepare("DELETE FROM calificacion WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
