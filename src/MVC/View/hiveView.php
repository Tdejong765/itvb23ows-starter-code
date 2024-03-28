<?php

include_once(__DIR__ . '/../../MVC/Controller/gameController.php');


class hiveView {

    private $gameController;

    public function __construct($gameController){
        $this->gameController = $gameController;
    }
    
    public function getAvailablePositions(){
        $to = [];
        foreach ($this->gameController->getOffsets() as $pq) {
            foreach (array_keys($this->gameController->getBoard()) as $pos) {
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


    public function showBoard(){
        $min_p = 1000;
        $min_q = 1000;
        foreach ($this->gameController->getBoard() as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p){
                $min_p = $pq[0];
            }

            if ($pq[1] < $min_q){
                $min_q = $pq[1];
            }
        }
        foreach (array_filter($this->gameController->getBoard()) as $pos => $tile) {
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

    public function showHand($BlackOrWhite){
        foreach ($this->gameController->getHand()[$BlackOrWhite] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player'.$BlackOrWhite.'"><span>'.$tile."</span></div> ";
            }
        }
    }

    public function showTurn(){
        if ($this->gameController->getPlayer() == 0){
            echo "White";
        }
        else {
            echo "Black";
        }
    }

    public function showTiles($player){
        foreach ($this->gameController->getHand()[$player] as $tile => $ct) {
            echo "<option value=\"$tile\">$tile</option>";
        }
    }

    public function showAvailablePositions(){
        $to = $this->getAvailablePositions();
        foreach ($to as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
    }

    public function showGame(){
        $result = $this->gameController->refreshGame();

        if ($result) {
            while ($row = $result->fetch_array()) {
                echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
            }
        } else {
            echo "Error fetching game data.";
        }
    }

    public function handleFormSubmission() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["move_submit"])) {
                $this->gameController->move();

                if ($this->gameController->getERROR() != NULL){
                    echo $this->gameController->getERROR();
                } else {
                    echo "Made move";
                }
            }

            elseif (isset($_POST["pass_submit"])) {
                $this->gameController->pass();
                echo "Pass form submitted!";
            }

            elseif (isset($_POST["play_submit"])) {
                $this->gameController->play();

                if ($this->gameController->getERROR() != NULL){
                    echo $this->gameController->getERROR();
                } else {
                    echo "Made a play";
                }
            }

            elseif (isset($_POST["restart_submit"])) {
                $this->gameController->restartGame();
                echo "Restart form submitted!";
            } 

            elseif (isset($_POST["undo_submit"])) {
                $this->gameController->undo();
                echo "undo form submitted!";
            } 

            else {
                // Handle unexpected form submissions
                echo "Invalid form submission!";
            }
        }
    }
}