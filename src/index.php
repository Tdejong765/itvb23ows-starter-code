<?php
    session_start();

    include_once 'HiveController.php';
    include_once 'hiveView.php';
    include_once 'hiveModel.php';

    $hiveModel = new hiveModel('mysql-db', 'username', 'password', 'hive', 3306);

    $hiveController = new hiveController($hiveModel);

    $hiveView = new hiveView($hiveController);


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="board">
            <?php
               $hiveView->showBoard();
            ?>
        </div>

        <div class="hand">
            White:
            <?php
                $hiveView->showHand(0);
            ?>
        </div>

        <div class="hand">
            Black:
            <?php
                $hiveView->showHand(1);
            ?>
        </div>

        <div class="turn">
            Turn: 
            <?php 
                $hiveView->showTurn();
            ?>
        </div>

        <form method="post" action="play.php">
            <select name="piece">
                <?php
                    $hiveView->showTiles();
                ?>
            </select>
            <select name="to">
                <?php
                    $hiveView->showAvailablePositions();
                ?>
            </select>
            <input type="submit" value="Play">
        </form>
        <form method="post" action="move.php">
            <select name="from">
                <?php
                    $hiveView->showAvailablePositions();
                ?>
            </select>
            <select name="to">
                <?php
                    $hiveView->showAvailablePositions();
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
               $hiveView->showGame();
            ?>
        </ol>

        <form method="post" action="undo.php">
            <input type="submit" value="Undo">
        </form>

    </body>
</html>

