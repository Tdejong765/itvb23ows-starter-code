<?php
    include_once 'MVC/sessionManager.php';
    include_once 'MVC/HiveController.php';
    include_once 'MVC/hiveView.php';
    include_once 'MVC/hiveModel.php';

    $sessionManager = new sessionManager();
    $hiveModel = new hiveModel('mysql-db', 'username', 'password', 'hive', 3306);
    $hiveController = new hiveController($hiveModel, $sessionManager);
    $hiveView = new hiveView($hiveController);
    $hiveView->handleFormSubmission();

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

        <form method="post">
            <select name="piece">
                <?php
                    $hiveView->showTiles(0);
                ?>
            </select>
            <select name="to">
                <?php
                    $hiveView->showAvailablePositions();
                ?>
            </select>
            <input type="submit" name="play_submit" value="Play">
        </form>
        <form method="post">
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
            <input type="submit" name="move_submit" value="Move">
        </form>

        <form method="post">
            <input type="submit" name="pass_submit" value="Pass">
        </form>

        <form method="post">
            <input type="submit" name="restart_submit"  value="Restart">
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

        <form method="post">
            <input type="submit" name="undo_submit" value="Undo">
        </form>

    </body>
</html>

