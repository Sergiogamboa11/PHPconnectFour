<?php 

//$home = "http://localhost:8612/ConnectFourPHP/src";
//$string = @file_get_contents($home . "/info/index.php");

//define('STRATEGY', 'strategy'); // constant    $strategies = array("Smart", "Random"); // supported strategies
//if (!array_key_exists(STRATEGY, $_GET)) { /* write code here */  exit; }

class move{
    public $slot = 1; //index of slot
    public $isWin = false;
    public $isDraw = false;
    public $row = array();
}

class ack_move{
    public $slot = 1; //index of slot
    public $isWin = false;
    public $isDraw = false;
    public $win = array();
}

class Play{
    public $response = true;
    public $ack_move;
    public $move;
}

$u = new Play();
$u->move = new move();
$u->ack_move = new ack_move();

$pid = htmlspecialchars($_GET["pid"]);
$move = htmlspecialchars($_GET["move"]);
$my_file = $pid.'.json';

if(file_exists($my_file)){
    $handleR = fopen($my_file, 'r') or die('Cannot open file to read: '.$my_file);
    $content = fread($handleR, 500);
    $board = json_decode($content);
    fclose($handleR);
}
else{
    $board = makeBoard();
}

// Acknowledge player move
for($i = 0; $i < sizeof($board[$move]); $i++){
    if($board[$move][$i] == 0){
        $board[$move][$i] = 1;
        $u->ack_move->slot = $move;
        if (winChecker($move, $i, $board, 1)) {//player 1 is user
            $u->ack_move->isWin = true;
        }
        break;
    }
}

//Make random move
// $randomnum = rand(0, 6);
$randomnum = 1;
while(isColFull($randomnum, $board)){
    $randomnum = rand(0, 6);
}

for($i = 0; $i < sizeof($board[$randomnum]); $i++){
    if($board[$randomnum][$i] == 0){
        $board[$randomnum][$i] = 2;
        $u->move-> slot = $randomnum;
        if (winChecker($randomnum, $i, $board, 2)) {//player 2 is server
            $u->move->isWin = true;
        }
        break;
    }
}

// echo $my_file;
$handle = fopen($my_file, 'w') or die('Cannot open file to write: '.$my_file);
fwrite($handle, json_encode($board));
fclose($handle);

echo json_encode($u); // Important output
// echo isColFull($randomnum, $board);


/**
 * Creates an empty 2d array representing the board
 * @return number[][] The 2d array representing the board
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

function makeMove($input){
}

/**
 * @param int $x The x coordinate of the slot
 * @param int $y The y coordinate of the slot
 * @param int[][] $board The game's current board
 * @param int $player The player's number (1 for user, 2 for server)
 * @return boolean Returns true if a player has won, false otherwise
 */
function winChecker($x, $y, $board, $player){
    $total = 0;
    for($cols = $y; $cols < sizeof($board[$x]); $cols++){ //vertical check
        if($board[$x][$cols] == $player){
            $total+=1;
        }
        else
            break;
    }
    for($cols = $y; $cols < sizeof($board[$x]); $cols--){
        if($board[$x][$cols] == $player){
            $total+=1;
        }
        else{
            break;
        }
    }
    if($total>=5){
        return true;
    }
    
    $total = 0;
    for($rows = $x; $rows < sizeof($board); $rows++){ //horizontal check
        if($board[$rows][$y] == $player){
            $total+=1;
        }
        else
            break;
    }
    for($rows = $x; $rows < sizeof($board); $rows--){
        if($board[$rows][$y] == $player){
            $total+=1;
        }
        else{
            break;
        }
    }
    if($total>=5){
        return true;
    }
    
    $total = 0;
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols++, $count++){ //diagonal 1
        if(sizeof($board) > $board[$x+ $count][$cols]){
            if($board[$x+ $count][$cols] == $player){
                $total+=1;
            }
            else
                break;
        }
    }
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols--, $count++){
        if(sizeof($board) > $board[$x-$count][$cols]){
            if($board[$x-$count][$cols] == $player){
                $total+=1;
            }
            else
                break;
        }
    }
    if($total>=5){
        return true;
    }
    
    $total = 0;
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols++, $count++){ //diagonal 1
        if(sizeof($board) > $board[$x-$count][$cols]){
            if($board[$x-$count][$cols] == $player){
                $total+=1;
            }
            else
                break;
        }
    }
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols--, $count++){
        if(sizeof($board) > $board[$x+$count][$cols]){
            if($board[$x+$count][$cols] == $player){
                $total+=1;
            }
            else
                break;
        }
    }
    if($total>=5){
        return true;
    }
    
    return false;
}

/**
 * This method checks if the specifies col is filled
 * @param int $col Number of the column
 * @param int[][] $board The game's current board
 * @return boolean True if column is full, false otherwise
 */
function isColFull($col, $board){
    if($board[$col][sizeof($board[$col])-1] != 0)
        return true;
    return false;
}

function isBoardFull(){
    return true;
}

?>

