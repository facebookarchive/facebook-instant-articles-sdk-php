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
        $expectedSubmissionStatusID = 1;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with('id')
            ->willReturn($expectedSubmissionStatusID);

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'published' => false,
                'development_mode' => false,
            ])
            ->willReturn($serverResponseMock);

        $resultSubmissionStatusID = $this->client->importArticle($this->article);
        $this->assertEquals($expectedSubmissionStatusID, $resultSubmissionStatusID);
    }

    public function testImportArticlePublished()
    {
        $expectedSubmissionStatusID = 1;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with('id')
            ->willReturn($expectedSubmissionStatusID);

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'published' => true,
                'development_mode' => false,
            ])
            ->willReturn($serverResponseMock);

        $resultSubmissionStatusID = $this->client->importArticle($this->article, true);
        $this->assertEquals($expectedSubmissionStatusID, $resultSubmissionStatusID);
    }

    /**
     * Tests that a re-scrape is performed when requested at article import.
     */
    public function testImportArticleRescrape()
    {
        $expectedSubmissionStatusID = 1;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with('id')
            ->willReturn($expectedSubmissionStatusID);

        $this->facebook
            ->expects($this->at(0))
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'published' => true,
                'development_mode' => false,
            ])
            ->willReturn($serverResponseMock);

        $this->facebook
            ->expects($this->at(1))
            ->method('post')
            ->with('/', [
                'id' => $this->article->getCanonicalURL(),
                'scrape' => 'true',
            ])
            ->willReturn($serverResponseMock);

        $resultSubmissionStatusID = $this->client->importArticle($this->article, true, true);
        $this->assertEquals($expectedSubmissionStatusID, $resultSubmissionStatusID);
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

        $expectedSubmissionStatusID = 1;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with('id')
            ->willReturn($expectedSubmissionStatusID);

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'published' => false,
                'development_mode' => true,
            ])
            ->willReturn($serverResponseMock);

        $resultSubmissionStatusID = $this->client->importArticle($this->article);
        $this->assertEquals($expectedSubmissionStatusID, $resultSubmissionStatusID);
    }

    public function testImportArticleDevelopmentModePublished()
    {
        $this->client = new Client(
            $this->facebook,
            "PAGE_ID",
            true // developmentMode
        );

        $expectedSubmissionStatusID = 1;

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->once())
            ->method('getField')
            ->with('id')
            ->willReturn($expectedSubmissionStatusID);

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID' . Client::EDGE_NAME, [
                'html_source' => $this->article->render(),
                'published' => false,
                'development_mode' => true,
            ])
            ->willReturn($serverResponseMock);

        $resultSubmissionStatusID = $this->client->importArticle($this->article, true);
        $this->assertEquals($expectedSubmissionStatusID, $resultSubmissionStatusID);
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

    public function testDevelopmentModeGetArticleIDFromCanonicalURL()
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
            ->with($this->equalTo('development_instant_article'))
            ->willReturn($instantArticleMock);
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('?id='.$canonicalURL.'&fields=development_instant_article'))
            ->willReturn($serverResponseMock);

        // Set up new client in development mode
        $this->client = new Client(
            $this->facebook,
            "PAGE_ID",
            true // developmentMode
        );

        $articleID = $this->client->getArticleIDFromCanonicalURL($canonicalURL);
        $this->assertEquals($expectedArticleID, $articleID);
    }

    public function testDevelopmentModeGetArticleIDFromNotFoundCanonicalURL()
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
            ->with($this->equalTo('development_instant_article'))
            ->willReturn(null);
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('?id='.$canonicalURL.'&fields=development_instant_article'))
            ->willReturn($serverResponseMock);

        // Set up new client in development mode
        $this->client = new Client(
            $this->facebook,
            "PAGE_ID",
            true // developmentMode
        );

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

    public function testGetSubmissionStatus()
    {
        $submissionStatusID = '456';

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $graphNodeMock
            ->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('errors')],
                [$this->equalTo('status')]
            )
            ->will($this->onConsecutiveCalls(
                [
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
                ],
                'success'
            ));
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo($submissionStatusID . '?fields=status,errors'))
            ->willReturn($serverResponseMock);

        $status = $this->client->getSubmissionStatus($submissionStatusID);
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

    public function testGetReviewSubmissionStatus()
    {
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
            ->with($this->equalTo('instant_articles_review_status'))
            ->willReturn('NOT_SUBMITTED');
        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);

        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with('me?fields=instant_articles_review_status')
            ->willReturn($serverResponseMock);

        $result = $this->client->getReviewSubmissionStatus();
        $this->assertEquals('NOT_SUBMITTED', $result);
    }

    public function testGetArticlesURLs()
    {
        $mockedMap = array(
            array('canonical_url'=>'http://url.com/1'),
            array('canonical_url'=>'http://url.com/2'),
            array('canonical_url'=>'http://url.com/3')
        );

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphEdge')
            ->willReturn($mockedMap);

        $this->facebook
            ->expects($this->once())
            ->method('get')
            ->with('me/instant_articles?fields=canonical_url&development_mode=false&limit=10')
            ->willReturn($serverResponseMock);


        $expected = array(
            'http://url.com/1',
            'http://url.com/2',
            'http://url.com/3'
        );

        $result = $this->client->getArticlesURLs();
        $this->assertEquals($expected, $result);
    }

    public function testClaimURL()
    {
        $url = 'example.com';

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('error')],
                [$this->equalTo('success')]
            )
            ->will($this->onConsecutiveCalls(
                null,
                true
            ));

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID/claimed_urls?url=' . $url)
            ->willReturn($serverResponseMock);

        $result = $this->client->claimURL($url);
    }

    public function testClaimURLWithProtocl()
    {
        $url = 'http://example.com';

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('error')],
                [$this->equalTo('success')]
            )
            ->will($this->onConsecutiveCalls(
                null,
                true
            ));

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID/claimed_urls?url=example.com')
            ->willReturn($serverResponseMock);

        $result = $this->client->claimURL($url);
    }

    public function testClaimURLError()
    {
        $url = 'example.com';
        $error_user_msg = "Error message";

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('error')],
                [$this->equalTo('success')]
            )
            ->will($this->onConsecutiveCalls(
                array( 'error_user_msg' => $error_user_msg ),
                false
            ));

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID/claimed_urls?url=' .$url)
            ->willReturn($serverResponseMock);

        $this->setExpectedException('\Facebook\InstantArticles\Client\ClientException');

        $result = $this->client->claimURL($url);
    }

    public function testSubmitForReview()
    {
        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('error')],
                [$this->equalTo('success')]
            )
            ->will($this->onConsecutiveCalls(
                null,
                true
            ));

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID/?instant_articles_submit_for_review=true')
            ->willReturn($serverResponseMock);

        $result = $this->client->submitForReview();
    }

    public function testSubmitForReviewError()
    {
        $error_user_msg = "Error message";

        $serverResponseMock =
            $this->getMockBuilder('Facebook\FacebookResponse')
                ->disableOriginalConstructor()
                ->getMock();
        $graphNodeMock =
            $this->getMockBuilder('Facebook\GraphNodes\GraphNode')
                ->disableOriginalConstructor()
                ->getMock();

        $serverResponseMock
            ->expects($this->once())
            ->method('getGraphNode')
            ->willReturn($graphNodeMock);
        $graphNodeMock
            ->expects($this->exactly(2))
            ->method('getField')
            ->withConsecutive(
                [$this->equalTo('error')],
                [$this->equalTo('success')]
            )
            ->will($this->onConsecutiveCalls(
                array( 'error_user_msg' => $error_user_msg ),
                false
            ));

        $this->facebook
            ->expects($this->once())
            ->method('post')
            ->with('PAGE_ID/?instant_articles_submit_for_review=true')
            ->willReturn($serverResponseMock);

        $this->setExpectedException('\Facebook\InstantArticles\Client\ClientException');

        $result = $this->client->submitForReview();
    }
}
