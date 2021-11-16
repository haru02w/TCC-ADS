<?php
    namespace Rafa\Socket;

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    
    class Chat implements MessageComponentInterface {
        protected $clients;
        private $users;
    
        public function __construct() {
            $this->clients = new \SplObjectStorage;
            $this->users = [];
        }
    
        public function onOpen(ConnectionInterface $conn) {
            parse_str($conn->httpRequest->getUri()->getQuery(), $queryValues);

            $ustoken = $queryValues["ustoken"];
            $type = $queryValues["t"];
            $setoken = $queryValues["setoken"];
            
            if(empty($ustoken) OR empty($type) OR empty($setoken)) {
                $conn->close(3001);
            }

            if($type == "d") {
                $type = "DEVELOPER";
                $sql = "SELECT * FROM TB_DEVELOPER WHERE TOKENCHAT = ?";
            }
            else if($type == "c") {
                $type = "CUSTOMER";
                $sql = "SELECT * FROM TB_CUSTOMER WHERE TOKENCHAT = ?";
            }
            else {
                $conn->close(3001);
            }
            
            require("connection.php");
            require_once("functionschat.php");

            $userinfo = mysqli_fetch_assoc(searchConnectedUser($connmysqli, $ustoken, $sql));
            $serviceinfo = mysqli_fetch_assoc(searchServiceByToken($connmysqli, $setoken));

            if(is_null($userinfo) OR is_null($serviceinfo)) {
                $conn->close(3001);
            }

            $id = $userinfo["ID_$type"];
            $iddev = $serviceinfo["COD_DEVELOPER"];
            $idcus = $serviceinfo["COD_CUSTOMER"];

            if ($type == "CUSTOMER") {
                if ($idcus !== $id) {
                    $conn->close(3001);
                }
            } 
            else if ($type == "DEVELOPER") {
                if ($iddev !== $id) {
                    $conn->close(3001);
                }
            }

            if($serviceinfo["STATUS"] <= 1 OR $serviceinfo["STATUS"] >= 3) {
                $conn->close(3001);
            }

            foreach($this->users as $users) {
                if($users["idbd"] == $id AND $users["setoken"] == $setoken) {
                    $users["conn"]->close(3000);
                }
            }

            $this->clients->attach($conn);
            $this->users[$conn->resourceId] = array(
                "resId" => $conn->resourceId, 
                "conn" => $conn, 
                "ustoken" => $ustoken,
                "name" => $userinfo["NAME"],
                "type" => $type,
                "setoken" => $setoken,
                "idbd" => $id
            );
            echo "Nova conexao! ({$conn->resourceId}) ({$queryValues['ustoken']})\n";
        }
    
        public function onMessage(ConnectionInterface $from, $msg) {
            require("connection.php");
            $currentuser = $this->users[$from->resourceId];
            $currenttoken = $currentuser["ustoken"];
            $service = $currentuser["setoken"];
            $sender = $currentuser["idbd"];
            $type = $currentuser["type"];
            $msg = json_decode($msg, true);

            $serviceinfo = mysqli_fetch_assoc(searchServiceByToken($connmysqli, $service));

            if($type == "DEVELOPER") {
                $user = searchInfoCusByService($serviceinfo["COD_CUSTOMER"], $connmysqli);
                $receiver = $user["ID_CUSTOMER"];
            }
            else {
                $user = searchInfoDevByService($serviceinfo["COD_DEVELOPER"], $connmysqli);
                $receiver = $user["ID_DEVELOPER"];
            }

            storeMessage($sender, $receiver, $service, $msg["msg"], $connmysqli);

            foreach ($this->users as $users) {
                if($service == $users["setoken"] AND $currenttoken != $users["ustoken"]) {
                    $msg = json_encode($msg);
                    $users["conn"]->send($msg);
                }
            }
        }
    
        public function onClose(ConnectionInterface $conn) {
            unset($this->users[$conn->resourceId]);
            $this->clients->detach($conn);
            echo "A conexao {$conn->resourceId} foi desconectada\n";
        }
    
        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "Ocorreu um erro: {$e->getMessage()}\n";
            $conn->close();
        }
    }