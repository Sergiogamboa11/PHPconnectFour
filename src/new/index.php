<?php

class NewGame {
    public $response = false;
    public $pid = 0;
}

$strat = htmlspecialchars($_GET["strategy"]);
if ($strat == "Smart") {
    //echo "works!";
}

$u = new NewGame();
$u->response = true;
$u->pid = uniqid();

echo json_encode($u)
?>