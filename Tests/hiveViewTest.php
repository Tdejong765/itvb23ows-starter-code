<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/MVC/Controller/gameController.php';
require_once __DIR__ . '/../src/MVC/Controller/boardController.php';
require_once __DIR__ . '/../src/MVC/Controller/sessionController.php';
require_once __DIR__ . '/../src/MVC/View/hiveView.php';
require_once __DIR__ . '/../src/MVC/Model/hiveModel.php';


class hiveViewTest extends TestCase {

    public function testShowTiles() {
        $gameControllerMock = $this->getMockBuilder(GameController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gameControllerMock->method('getHand')
            ->willReturn([
                'player1' => ['stone1' => 2, 'stone2' => 1],
                'player2' => ['stone3' => 3, 'stone4' => 1],
            ]);

        $hiveView = new hiveView($gameControllerMock);

        $options = $hiveView->showTiles('player1');

        $expectedOptions = [
            '<option value="stone1">stone1</option>',
            '<option value="stone2">stone2</option>',
        ];

        $this->assertEquals($expectedOptions, $options);
    }


    public function testShowAvailablePositions(): void
    {   
        $boardControllerMock = $this->createMock(boardController::class);
        $boardControllerMock->expects($this->once())
            ->method('getOffsets')
            ->willReturn([[1, 0], [0, 1]]);

        $gameControllerMock = $this->createMock(gameController::class);
        $gameControllerMock->expects($this->once())
            ->method('getOffsets')
            ->willReturn([[1, 0], [0, 1]]);

        $hiveView = $this->getMockBuilder(HiveView::class)
            ->setConstructorArgs([$gameControllerMock])
            ->getMock();

        $hiveView->expects($this->once())->method('getAvailablePositions');
        $hiveView->showAvailablePositions();
    }
}
