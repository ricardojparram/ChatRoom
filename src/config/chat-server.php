<?php  

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use model\Chat;
use config\systemConfig;
    
    require '././vendor/autoload.php';

    $config = new systemConfig();
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        $config->_SOCKET_PORT(),
        $config->_SOCKET_IP()
    );

    $server->run();

?>