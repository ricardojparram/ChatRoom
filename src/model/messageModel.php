<?php

namespace model;
use config\DBconnect;

class messageModel extends DBconnect
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Guardar un mensaje; devuelve el ID del registro insertado
     */
    public function saveMessage($username, $message)
    {
        try {
            $stmt = $this->con->prepare(
                'INSERT INTO messages (username, message) VALUES (?, ?)'
            );
            $stmt->bindValue(1, $username);
            $stmt->bindValue(2, $message);
            $stmt->execute();
            return (int) $this->con->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener los últimos N mensajes (incluye id, edited y deleted)
     */
    public function getMessages($limit = 100)
    {
        try {
            $stmt = $this->con->prepare(
                'SELECT id, username, message, created_at, edited, deleted
                 FROM messages
                 ORDER BY created_at ASC
                 LIMIT ?'
            );
            $stmt->bindValue(1, (int) $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Editar un mensaje. Solo puede editarlo su propio dueño.
     */
    public function updateMessage($id, $username, $newMessage)
    {
        try {
            $stmt = $this->con->prepare(
                'UPDATE messages SET message = ?, edited = 1
                 WHERE id = ? AND username = ? AND deleted = 0'
            );
            $stmt->bindValue(1, $newMessage);
            $stmt->bindValue(2, (int) $id, \PDO::PARAM_INT);
            $stmt->bindValue(3, $username);
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Soft-delete de un mensaje. Solo puede borrarlo su propietario.
     */
    public function deleteMessage($id, $username)
    {
        try {
            $stmt = $this->con->prepare(
                'UPDATE messages SET deleted = 1, message = NULL
                 WHERE id = ? AND username = ?'
            );
            $stmt->bindValue(1, (int) $id, \PDO::PARAM_INT);
            $stmt->bindValue(2, $username);
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } catch (\PDOException $e) {
            return false;
        }
    }
}

?>