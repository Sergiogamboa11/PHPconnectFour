<?php

class NewGame {
    public $response = false;
    public $pid = "";
}

$strat = htmlspecialchars($_GET["strategy"]);
$u = new NewGame();
$u->response = true;
if ($strat == "Smart") {
    $u->pid = "Smrt".uniqid();
}
if ($strat == "Random") {
    $u->pid = "Rndm".uniqid();
}

echo json_encode($u)
?>