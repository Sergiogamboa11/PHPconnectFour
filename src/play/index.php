<?php 
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
    public $row = array();
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
$my_file = '../writable/'.$pid.'.json';

if ($pid == "") {
    $u->response = false;
    unset($u->move);
    unset($u->ack_move);
    $u->reason = "Pid not specified";
    goto a;
}
if ($move == "") {
    $u->response = false;
    unset($u->move);
    unset($u->ack_move);
    $u->reason = "Move not specified";
    goto a;
}
if ($move < 0 || $move > 6) {
    $u->response = false;
    unset($u->move);
    unset($u->ack_move);
    $u->reason = "Invalid move";
    goto a;
}

if(file_exists($my_file)){ //If game already exists, retrieve board
    $handleR = fopen($my_file, 'r') or die('Cannot open file to read: '.$my_file);
    $content = fread($handleR, 500);
    $board = json_decode($content);
    fclose($handleR);
}
else{ //Otherwise create new board
//     $board = makeBoard();
    $u->response = false;
    unset($u->move);
    unset($u->ack_move);
    $u->reason = "Unknown pid";
    goto a;
}

// Update board with player move
for($i = 0; $i < sizeof($board[$move]); $i++){
    if($board[$move][$i] == 0){
        $board[$move][$i] = 1;
        $u->ack_move->slot = $move;
        $winResult = winChecker($move, $i, $board, 1);
        if (array_shift($winResult)) {//player 2 is server
            $u->ack_move->isWin = true;
            $u->ack_move->row = $winResult;
        }
        break;
    }
}

$randomnum = 0;
if (strpos($pid, 'Rndm') !== FALSE){//Make random move
    $randomnum = rand(0, 6);
    while(isColFull($randomnum, $board)){ //if column is filled, roll a new number
        $randomnum = rand(0, 6);
    }
}
else if (strpos($pid, 'Smrt') !== FALSE){ //Make smart move
    $randomnum = smartNumber($board);
     while(isColFull($randomnum, $board)){ //if column is filled, roll a new number
         $randomnum = rand(0, 6);
     }
    smartNumber($board);
}

//Update board with server move
for($i = 0; $i < sizeof($board[$randomnum]); $i++){
    if($board[$randomnum][$i] == 0){
        $board[$randomnum][$i] = 2;
        $u->move-> slot = $randomnum;
         $winResult = winChecker($randomnum, $i, $board, 2);
         if (array_shift($winResult)) {//player 2 is server
             $u->move->row = $winResult;
             $u->move->isWin = true;
         }
        break;
    }
}

//Write board changes to file
$handle = fopen($my_file, 'w') or die('Cannot open file to write: '.$my_file);
fwrite($handle, json_encode($board));
fclose($handle);

if( $u->ack_move->isWin == false && $u->move->isWin == false){
    $u->ack_move->isDraw = $u->move->isDraw = drawCheck($board);
}

a:
    echo json_encode($u); // json output

/**
 * This function checks for win conditions and returns an array with a true
 * or false in the first index, and if true, the coordinates of the winning row
 * in the rest of the array
 * @param int $x The x coordinate of the slot
 * @param int $y The y coordinate of the slot
 * @param int[][] $board The game's current board
 * @param int $player The player's number (1 for user, 2 for server)
 * @return array $array Containing true in the first index if player won with the coordinates of winning row following, and false otherwise
 */
function winChecker($x, $y, $board, $player){
    $array = array();
    $total = 0;
    for($cols = $y; $cols < sizeof($board[$x]); $cols++){ //vertical check
        if($board[$x][$cols] == $player){
            $total+=1;
            array_push($array, $x, $cols);
        }
        else
            break;
    }
    for($cols = $y; $cols < sizeof($board[$x]); $cols--){
        if($board[$x][$cols] == $player){
            if($cols != $y){
                array_push($array, $x, $cols);
                $total+=1;
            }
        }
        else{
            break;
        }
    }
    if($total>=4){
        array_unshift($array, true); //works
        return $array;
    }
    
    $array = array();
    $total = 0;
    for($rows = $x; $rows < sizeof($board); $rows++){ //horizontal check
        if($board[$rows][$y] == $player){
            $total+=1;
            array_push($array, $rows, $y);
        }
        else
            break;
    }
    for($rows = $x; $rows < sizeof($board); $rows--){
        if($board[$rows][$y] == $player){
            if($rows != $x){
                array_push($array, $rows, $y);
                $total+=1;
            }
        }
        else{
            break;
        }
    }
    if($total>=4){
        array_unshift($array, true); //works
        return $array;
    }
    
    $array = array();
    $total = 0;
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols++, $count++){ //diagonal /
        if(sizeof($board) > $board[$x+ $count][$cols]){
            if($board[$x+ $count][$cols] == $player){
                    array_push($array, $x+ $count, $cols);
                    $total+=1;
            }
            else
                break;
        }
    }
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols--, $count++){
        if(sizeof($board) > $board[$x-$count][$cols]){
            if($board[$x-$count][$cols] == $player){
                if($cols != $y){
                    array_push($array, $x- $count, $cols);
                    $total+=1;
                }
            }
            else
                break;
        }
    }
    if($total>=4){
        array_unshift($array, true); //works
        return $array;
    }
    
    $array = array();
    $total = 0;
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols++, $count++){ //diagonal \
        if(sizeof($board) > $board[$x-$count][$cols]){
            if($board[$x-$count][$cols] == $player){
                array_push($array, $x- $count, $cols);
                $total+=1;
            }
            else
                break;
        }
    }
    for($cols = $y, $count = 0; $cols < sizeof($board[$x]); $cols--, $count++){
        if(sizeof($board) > $board[$x+$count][$cols]){
            if($board[$x+$count][$cols] == $player){
                if($cols != $y){
                    array_push($array, $x+ $count, $cols);
                    $total+=1;
                }
            }
            else
                break;
        }
    }
    if($total>=4){
        array_unshift($array, true); //works
        return $array;
    } 
    $array = array(false);
    return $array;
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

/**
 * This method checks if there has been a draw
 * @param int[][] $board The game's current board
 * @return boolean True if there's a draw, false otherwise
 */
function drawCheck($board){
    $numFull = 0;
    for($c = 0; $c < sizeof($board); $c++){
        if(isColFull($c, $board) == true){
            $numFull ++;
        }
    }
    if($numFull > 6){
        return true;
    }
    else return false;
}

/**
 * This method picks a move for the Smart strategy
 * @param int[][] $board The game's current board
 * @return number The number of the col to make a move on
 */
function smartNumber($board){
    $num = rand(0,6);
    $win = false;
    for($c = 0; $c < sizeof($board); $c++){ //Check if player 1 is about to win
        for($i = 0; $i < sizeof($board[$c]); $i++){
            if($board[$c][$i] == 0){
             $board[$c][$i] = 1;
             $win = array_shift(winChecker($c, $i, $board, 1)); 
             $board[$c][$i] = 0;
             break;
            }
        }
        if($win == true){
            $num = $c;
            return $num;
        }
    }
    for($c = 0; $c < sizeof($board); $c++){ //check if player 2 can make a winning move
        for($i = 0; $i < sizeof($board[$c]); $i++){
            if($board[$c][$i] == 0){
                $board[$c][$i] = 2;
                $win = array_shift(winChecker($c, $i, $board, 2));
                $board[$c][$i] = 0;
                break;
            }
        }
        if($win == true){
            $num = $c;
            return $num;
        }
    }
    return $num;
}

?>

