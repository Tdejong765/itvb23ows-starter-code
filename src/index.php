<?php
    session_start();

    include_once 'Hive.php';

    $Hive = new Hive($_SESSION['board'], $_SESSION['player'], $_SESSION['hand']);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" type="text/css" href="style/style.css">
    </head>
    <body>
        <div class="board">
            <?php
               $Hive->showBoard();
            ?>
        </div>

        <div class="hand">
            White:
            <?php
                $Hive->showHand($hand, 0);
            ?>
        </div>

        <div class="hand">
            Black:
            <?php
                $Hive->showHand($hand, 1);
            ?>
        </div>

        <div class="turn">
            Turn: 
            <?php 
                $Hive->showTurn();
            ?>
        </div>

        <form method="post" action="play.php">
            <select name="piece">
                <?php
                    $Hive->showTiles();
                ?>
            </select>
            <select name="to">
                <?php
                    $Hive->showAvailablePositions();
                ?>
            </select>
            <input type="submit" value="Play">
        </form>
        <form method="post" action="move.php">
            <select name="from">
                <?php
                    $Hive->showAvailablePositions();
                ?>
            </select>
            <select name="to">
                <?php
                    $Hive->showAvailablePositions();
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
               $Hive->showGame();
            ?>
        </ol>

        <form method="post" action="undo.php">
            <input type="submit" value="Undo">
        </form>

    </body>
</html>

