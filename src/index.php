<?php
    session_start();

    include_once 'HiveController.php';
    include_once 'HiveView.php';

    $HiveController = new HiveController($_SESSION['board'], $_SESSION['player'], $_SESSION['hand']);
    $HiveView = new HiveView($HiveController);


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hive</title>
    </head>
    <body>
        <div class="board">
            <?php
               $HiveView->showBoard();
            ?>
        </div>

        <div class="hand">
            White:
            <?php
                $HiveView->showHand(0);
            ?>
        </div>

        <div class="hand">
            Black:
            <?php
                $HiveView->showHand(1);
            ?>
        </div>

        <div class="turn">
            Turn: 
            <?php 
                $HiveView->showTurn();
            ?>
        </div>

        <form method="post" action="play.php">
            <select name="piece">
                <?php
                    $HiveView->showTiles();
                ?>
            </select>
            <select name="to">
                <?php
                    $HiveView->showAvailablePositions();
                ?>
            </select>
            <input type="submit" value="Play">
        </form>
        <form method="post" action="move.php">
            <select name="from">
                <?php
                    $HiveView->showAvailablePositions();
                ?>
            </select>
            <select name="to">
                <?php
                    $HiveView->showAvailablePositions();
                ?>
            </select>
            <input type="submit" value="Move">
        </form>

        <form method="post" action="pass.php">
            <input type="submit" value="Pass">
        </form>

        <form method="post" action="restart.php">
            <input type="submit" value="Restart">
        </form>

        <strong>
            <?php 
                if (isset($_SESSION['error'])){
                echo $_SESSION['error'];
            }
                unset($_SESSION['error']); ?></strong>
        <ol>
            <?php
               $HiveView->showGame();
            ?>
        </ol>

        <form method="post" action="undo.php">
            <input type="submit" value="Undo">
        </form>

    </body>
</html>

