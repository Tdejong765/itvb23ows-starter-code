
<style>
<?php include '../style.css'; ?>
</style>

<?php

include_once 'HiveController.php';

class hiveView {

    private $HiveController;

    public function __construct($HiveController){
        $this->HiveController = $HiveController;
    }
    
    function getAvailablePositions(){
        $to = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            foreach (array_keys($this->HiveController->getBoard()) as $pos) {
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
        foreach ($this->HiveController->getBoard() as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p){
                $min_p = $pq[0];
            }

            if ($pq[1] < $min_q){
                $min_q = $pq[1];
            }
        }
        foreach (array_filter($this->HiveController->getBoard()) as $pos => $tile) {
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

    function showHand($BlackOrWhite){
        foreach ($this->HiveController->getHand()[$BlackOrWhite] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player'.$BlackOrWhite.'"><span>'.$tile."</span></div> ";
            }
        }
    }

    function showTurn(){
        if ($this->HiveController->getPlayer() == 0){
            echo "White";
        }
        else {
            echo "Black";
        }
    }

    function showTiles($player){
        foreach ($this->HiveController->getHand()[$player] as $tile => $ct) {
            echo "<option value=\"$tile\">$tile</option>";
        }
    }

    function showAvailablePositions(){
        $to = $this->getAvailablePositions();
        foreach ($to as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
    }

    function showGame(){
        while ( $this->HiveController->refreshGame()->fetch_array()) {
            echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
        }
    }

    function handleFormSubmission() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["move_submit"])) {
                $this->HiveController->move();
                echo "Move form submitted!";
            }

            elseif (isset($_POST["pass_submit"])) {
                $this->HiveController->pass();
                echo "Pass form submitted!";
            }

            elseif (isset($_POST["play_submit"])) {
                $this->HiveController->play();
                echo "Play form submitted!";
            }

            elseif (isset($_POST["restart_submit"])) {
                $this->HiveController->restart();
                echo "Restart form submitted!";
            } 

            elseif (isset($_POST["undo_submit"])) {
                $this->HiveController->undo();
                echo "undo form submitted!";
            } 

            else {
                // Handle unexpected form submissions
                echo "Invalid form submission!";
            }
        } else {
            
            header("Location: index.php");
            exit();
        }
    }
}