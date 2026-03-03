<?php
namespace model;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $users = []; // resourceId => username
    protected $connections = []; // username    => ConnectionInterface

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        // ── join: el cliente anuncia su username ─────────────────────────────
        if (isset($data['type']) && $data['type'] === 'join') {
            $username = $data['username'];
            $this->users[$from->resourceId] = $username;
            $this->connections[$username] = $from;
            echo "User joined: {$username} ({$from->resourceId})\n";
            $this->broadcastUserList();
            return;
        }

        // ── dm: mensaje privado ──────────────────────────────────────────────
        if (isset($data['type']) && $data['type'] === 'dm') {
            $to = $data['to'] ?? '';
            $message = $data['message'] ?? '';
            $fromUser = $this->users[$from->resourceId] ?? 'Unknown';

            if (isset($this->connections[$to])) {
                // Entregar al destinatario
                $this->connections[$to]->send(json_encode([
                    'type' => 'dm',
                    'from' => $fromUser,
                    'message' => $message,
                ]));
                // Confirmar al remitente
                $from->send(json_encode([
                    'type' => 'dm_sent',
                    'to' => $to,
                    'message' => $message,
                ]));
            } else {
                $from->send(json_encode([
                    'type' => 'dm_error',
                    'error' => "'{$to}' is not online.",
                ]));
            }
            return;
        }

        // ── Broadcast: mensaje normal de chat ────────────────────────────────
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            "Connection %d sending message to %d other connection%s\n",
            $from->resourceId,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $username = $this->users[$conn->resourceId] ?? null;
        if ($username) {
            unset($this->connections[$username]);
        }
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        echo "Connection {$conn->resourceId} has disconnected\n";
        $this->broadcastUserList();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function broadcastUserList()
    {
        $payload = json_encode([
            'type' => 'user_list',
            'users' => array_values($this->users),
        ]);
        foreach ($this->clients as $client) {
            $client->send($payload);
        }
    }
}
?>