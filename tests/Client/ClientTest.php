<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Client;

use Facebook\Facebook;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Paragraph;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $article;
    private $facebook;

    protected function setUp()
    {
        $this->facebook = $this->getMockBuilder('Facebook\Facebook')
            ->disableOriginalConstructor()
            ->getMock();
        $this->client = new Client(
            $this->facebook,
            "PAGE_ID",
            false // developmentMode
        );
        $this->article =
            InstantArticle::create()
                ->addChild(
                    Paragraph::create()
                        ->appendText('Test')
                );
    }

    public function testImportArticle()
    {
        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'take_live' => false,
                'development_mode' => false,
            ]);

        $this->client->importArticle($this->article);
    }

    public function testImportArticleTakeLive()
    {
        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'take_live' => true,
                'development_mode' => false,
            ]);

        $this->client->importArticle($this->article, true);
    }

    /**
     * Tests removing an article from an Instant Articles library.
     *
     * @covers Facebook\InstantArticles\Client\Client::removeArticle()
     */
    public function testRemoveArticle()
    {
        $canonicalURL = 'http://facebook.com';
        $articleID = '1';

        // Use a mocked client with stubbed getArticleIDFromCanonicalURL().
        $this->client = $this->getMockBuilder('Facebook\InstantArticles\Client\Client')
          ->setMethods(['getArticleIDFromCanonicalURL'])
          ->setConstructorArgs([
            $this->facebook,
            "PAGE_ID",
            true // developmentMode
          ])->getMock();

        $this->client
          ->expects($this->once())
          ->method('getArticleIDFromCanonicalURL')
          ->with($canonicalURL)
          ->willReturn($articleID);

        $this->facebook
          ->expects($this->once())
          ->method('delete')
          ->with($articleID);

        $this->client->removeArticle($canonicalURL);
    }

    public function testImportArticleDevelopmentMode()
    {
        $this->client = new Client(
            $this->facebook,
            "PAGE_ID",
            true // developmentMode
        );
        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'take_live' => false,
                'development_mode' => true,
            ]);

        $this->client->importArticle($this->article);
    }

    public function testImportArticleDevelopmentModeTakeLive()
    {
        $this->client = new Client(
            $this->facebook,
            "PAGE_ID",
            true // developmentMode
        );
        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'take_live' => false,
                'development_mode' => true,
            ]);

        $this->client->importArticle($this->article, true);
    }

    public function testGetArticleIDFromCanonicalURL()
    {
        $canonicalURL = "http://facebook.com";

        $expectedArticleID = 123;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();
        $instantArticleMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $instantArticleMock
            ->expects($this->once())
            ->method('getField')
            ->with($this->equalTo('id'))
            ->willReturn($expectedArticleID);
        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with($this->equalTo('instant_article'))
            ->willReturn($instantArticleMock);
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('?id='.$canonicalURL.'&fields=instant_article'))
            ->willReturn($serverResponseMock);

        $articleID = $this->client->getArticleIDFromCanonicalURL($canonicalURL);
        $this->assertEquals($expectedArticleID, $articleID);
    }

    public function testGetArticleIDFromNotFoundCanonicalURL()
    {
        $canonicalURL = "http://facebook.com";

        $expectedArticleID = null;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with($this->equalTo('instant_article'))
            ->willReturn(null);
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('?id='.$canonicalURL.'&fields=instant_article'))
            ->willReturn($serverResponseMock);

        $articleID = $this->client->getArticleIDFromCanonicalURL($canonicalURL);
        $this->assertEquals($expectedArticleID, $articleID);
    }

    public function testGetLastSubmissionStatus()
    {
        $articleID = '123';

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with($this->equalTo('most_recent_import_status'))
            ->willReturn([
                "status" => "success",
                "errors" => [
                    [
                        "level" => "warning",
                        "message" => "Test warning"
                    ],
                    [
                        "level" => "fatal",
                        "message" => "Test fatal"
                    ],
                    [
                        "level" => "error",
                        "message" => "Test error"
                    ],
                    [
                        "level" => "info",
                        "message" => "Test info"
                    ]
                ]
            ]);

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo($articleID . '?fields=most_recent_import_status'))
            ->willReturn($serverResponseMock);

        $status = $this->client->getLastSubmissionStatus($articleID);
        $this->assertEquals(InstantArticleStatus::SUCCESS, $status->getStatus());
        $this->assertEquals(
            ServerMessage::WARNING,
            $status->getMessages()[0]->getLevel()
        );
        $this->assertEquals(
            'Test warning',
            $status->getMessages()[0]->getMessage()
        );
        $this->assertEquals(
            $status->getMessages()[1]->getLevel(),
            ServerMessage::FATAL
        );
        $this->assertEquals(
            'Test fatal',
            $status->getMessages()[1]->getMessage()
        );
        $this->assertEquals(
            ServerMessage::ERROR,
            $status->getMessages()[2]->getLevel()
        );
        $this->assertEquals(
            'Test error',
            $status->getMessages()[2]->getMessage()
        );
        $this->assertEquals(
            ServerMessage::INFO,
            $status->getMessages()[3]->getLevel()
        );
        $this->assertEquals(
            'Test info',
            $status->getMessages()[3]->getMessage()
        );
    }
}
