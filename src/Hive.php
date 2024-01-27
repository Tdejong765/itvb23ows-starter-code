<?php

$GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

class GameModel
{
    private $board;
    private $player;
    private $hand;

    public function __construct($board, $player, $hand)
    {
        $this->board = $board;
        $this->player = $player;
        $this->hand = $hand;
    }

    // Getter for $board
    public function getBoard()
    {
        return $this->board;
    }

    // Setter for $board
    public function setBoard($board)
    {
        $this->board = $board;
    }

    // Getter for $player
    public function getPlayer()
    {
        return $this->player;
    }

    // Setter for $player
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    // Getter for $hand
    public function getHand()
    {
        return $this->hand;
    }

    // Setter for $hand
    public function setHand($hand)
    {
        $this->hand = $hand;
    }


    function move(){
        session_start();

        include_once 'util.php';

        $from = $_POST['from'];
        $to = $_POST['to'];

        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];
        unset($_SESSION['error']);

        if (!isset($board[$from])){
            $_SESSION['error'] = 'Board position is empty';
        }
        elseif($board[$from][count($board[$from])-1][0] != $player){
            $_SESSION['error'] = "Tile is not owned by player";
        }
        elseif($hand['Q']){
            $_SESSION['error'] = "Queen bee is not played";
        }
        else {
            $tile = array_pop($board[$from]);
            if (!hasNeighBour($to, $board)){
                $_SESSION['error'] = "Move would split hive";
            }
            else {
                $all = array_keys($board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach ($GLOBALS['OFFSETS'] as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }
                if ($all) {
                    $_SESSION['error'] = "Move would split hive";
                } else {
                    if ($from == $to){
                        $_SESSION['error'] = 'Tile must move';
                    }
                    elseif (isset($board[$to]) && $tile[1] != "B"){
                        $_SESSION['error'] = 'Tile not empty';
                    }
                    elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!slide($board, $from, $to)){
                            $_SESSION['error'] = 'Tile must slide';
                        }
                    }
                }
            }
            if (isset($_SESSION['error'])) {
                if (isset($board[$from])){
                    array_push($board[$from], $tile);
                }
                else {
                    $board[$from] = [$tile];
                }
            } else {
                if (isset($board[$to])){ 
                    array_push($board[$to], $tile);
                }
                else {
                    $board[$to] = [$tile];
                }
                $_SESSION['player'] = 1 - $_SESSION['player'];
                $db = include_once 'database.php';
                $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "move", ?, ?, ?, ?)');
                $stmt->bind_param('issis', $_SESSION['game_id'], $from, $to, $_SESSION['last_move'], get_state());
                $stmt->execute();
                $_SESSION['last_move'] = $db->insert_id;
            }
            $_SESSION['board'] = $board;
        }

        header('Location: index.php');
    }


    function pass(){
        session_start();

        $db = include_once 'database.php';
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "pass", null, null, ?, ?)');
        $stmt->bind_param('iis', $_SESSION['game_id'], $_SESSION['last_move'], get_state());
        $stmt->execute();
        $_SESSION['last_move'] = $db->insert_id;
        $_SESSION['player'] = 1 - $_SESSION['player'];
        
        header('Location: index.php');
    }


    function play(){
        session_start();

        include_once 'util.php';

        $piece = $_POST['piece'];
        $to = $_POST['to'];

        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];

        if (!$hand[$piece]){
            $_SESSION['error'] = "Player does not have tile";
        }
        elseif (isset($board[$to])){
            $_SESSION['error'] = 'Board position is not empty';
        }
        elseif (count($board) && !hasNeighBour($to, $board)){
            $_SESSION['error'] = "board position has no neighbour";
        }
        elseif (array_sum($hand) < 11 && !neighboursAreSameColor($player, $to, $board)){
            $_SESSION['error'] = "Board position has opposing neighbour";
        }
        elseif (array_sum($hand) <= 8 && $hand['Q']) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
            $_SESSION['hand'][$player][$piece]--;
            $_SESSION['player'] = 1 - $_SESSION['player'];
            $db = include_once 'database.php';
            $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "play", ?, ?, ?, ?)');
            $stmt->bind_param('issis', $_SESSION['game_id'], $piece, $to, $_SESSION['last_move'], get_state());
            $stmt->execute();
            $_SESSION['last_move'] = $db->insert_id;
        }

        header('Location: index.php');
    }


    function restart(){
        session_start();
        $_SESSION['board'] = [];
        $_SESSION['hand'] = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $_SESSION['player'] = 0;

        $db = include_once 'database.php';
        $db->prepare('INSERT INTO games VALUES ()')->execute();
        $_SESSION['game_id'] = $db->insert_id;

        header('Location: index.php');

    }


    function undo(){
        session_start();
        $db = include_once 'database.php';
        $stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array();
        $_SESSION['last_move'] = $result[5];
        set_state($result[6]);
        header('Location: index.php');
    }


    function isNeighbour($a, $b) {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1){ 
            return true;
        }
        if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1){
            return true;
        }
        if ($a[0] + $a[1] == $b[0] + $b[1]){
            return true;
        }
        return false;
    }
    

    function hasNeighBour($a, $board) {
        foreach (array_keys($board) as $b) {
            if (isNeighbour($a, $b)){ 
                return true;
            }   
        }
    }
    

    function neighboursAreSameColor($player, $a, $board) {
        foreach ($board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && isNeighbour($a, $b)){ 
                return false;
            }
        }
        return true;
    }
    

    function len($tile) {
        return $tile ? count($tile) : 0;
    }
    

    function slide($board, $from, $to) {
        if (!hasNeighBour($to, $board)){
            return false;
        }
        if (!isNeighbour($from, $to)){
            return false;
        }
    
        $b = explode(',', $to);
        $common = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if (isNeighbour($from, $p.",".$q)){
                $common[] = $p.",".$q;
            }
        }
        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]){
            return false;
        }
        return min(len($board[$common[0]]), len($board[$common[1]])) <= max(len($board[$from]), len($board[$to]));
    }


    function getAvailablePositions(){
        $to = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            foreach (array_keys($board) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);
            }
        }
        
        $to = array_unique($to);
        if (!count($to)){
            $to[] = '0,0';
        }
        return $to;
    }


    function showBoard(){
        $min_p = 1000;
        $min_q = 1000;
        foreach ($board as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p){
                $min_p = $pq[0];
            }

            if ($pq[1] < $min_q){
                $min_q = $pq[1];
            }
        }
        foreach (array_filter($board) as $pos => $tile) {
            $pq = explode(',', $pos);
            $pq[0];
            $pq[1];
            $h = count($tile);
            echo '<div class="tile player';
            echo $tile[$h-1][0];
            
            if ($h > 1){
                echo ' stacked';
            }
            
            echo '" style="left: ';
            echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
            echo 'em; top: ';
            echo ($pq[1] - $min_q) * 4;
            echo "em;\">($pq[0],$pq[1])<span>";
            echo $tile[$h-1][1];
            echo '</span></div>';
        }
    }

    function showHand($hand, $BlackOrWhite){
        foreach ($hand[$BlackOrWhite] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player'.$BlackOrWhite.'"><span>'.$tile."</span></div> ";
            }
        }
}

}