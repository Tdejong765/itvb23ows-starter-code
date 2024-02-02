<?php


use PHPUnit\Framework\TestCase;

use Controller\boardController;
use Controller\gameController;
use Controller\sessionController;
use Model\hiveModel;
use View\hiveView;


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
}
