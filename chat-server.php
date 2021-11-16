<?php
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Rafa\Socket\Chat;

    require 'vendor/autoload.php';
	
	$wss = new Ratchet\WebSocket\WsServer(new Rafa\Socket\Chat());
	$app = new Ratchet\Http\HttpServer($wss);

	$loop = \React\EventLoop\Factory::create();

	$secure_websockets = new \React\Socket\Server('0.0.0.0:8443', $loop);
	$secure_websockets = new \React\Socket\SecureServer($secure_websockets, $loop, [
		'local_cert' => '/etc/apache/server.crt',
		'local_pk' => '/etc/apache/server.key',
		'allow_self_signed' => true,
		'verify_peer' => false
	]);

	$secure_websockets_server = new \Ratchet\Server\IoServer($app, $secure_websockets, $loop);
	$wss->enableKeepAlive($secure_websockets_server->loop, 10);
	$secure_websockets_server->run();