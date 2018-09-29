<?php 

//$home = "http://localhost:8612/ConnectFourPHP/src";
//$string = @file_get_contents($home . "/info/index.php");

//define('STRATEGY', 'strategy'); // constant    $strategies = array("Smart", "Random"); // supported strategies
//if (!array_key_exists(STRATEGY, $_GET)) { /* write code here */  exit; }
//$strategy = $_GET[STRATEGY];

// error_reporting(E_ALL);
// ini_set('display_errors', 1);


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

$array = array(
    array(0, 1, 2, 3, 4, 5),
    array(6, 7, 8, 9, 10, 11),
    array(12, 13, 14, 15, 16, 17),
    array(18, 19, 20, 21, 22, 23),
    array(24, 25, 26, 27, 28, 29),
    array(30, 31, 32, 33, 34, 35),
    array(36, 37, 38, 39, 40, 41),
);




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
$randomnum = rand(0, 6);
for($i = 0; $i < sizeof($board[$randomnum]); $i++){
    if($board[$randomnum][$i] == 0){
        $board[$randomnum][$i] = 2;
        $u->move-> slot = $randomnum;
        break;
    }
}

// echo $my_file;
$handle = fopen($my_file, 'w') or die('Cannot open file to write: '.$my_file);
fwrite($handle, json_encode($board));
fclose($handle);

echo json_encode($u); // Important output

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

?>

