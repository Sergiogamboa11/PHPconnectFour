<?php
/**
 * @author Sergio Gamboa
 */
class NewGame {
    public $response = false;
    public $pid = "";
}

$strat = htmlspecialchars($_GET["strategy"]);
$u = new NewGame();

if ($strat == "Smart") {
    $u->response = true;
    $u->pid = "Smrt".uniqid();
}
else if ($strat == "Random") {
    $u->response = true;
    $u->pid = "Rndm".uniqid();
}
else if ($strat == "") {
    unset($u->pid);
    $u->reason = "Strategy not specified";
}
else{
    unset($u->pid);
    $u->reason = "Unknown strategy";
}

if($u->response == true){
    $my_file = '../writable/'.$u->pid.'.json';
    $handle = fopen($my_file, 'w') or die('Cannot open file to write: '.$my_file);
    $board = makeBoard();
    fwrite($handle, json_encode($board));
}

echo json_encode($u);

/**
 * Creates an int[][] representing the board
 * @return int[][] The populated int[][] representing the board
 */
function makeBoard(){
    $board = array(
        array(0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0),
    );
    return $board;
}
?>