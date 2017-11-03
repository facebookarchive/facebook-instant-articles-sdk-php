<?php

namespace Facebook\InstantArticles\Client;

class ClientExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testExtendsException()
    {
        $exception = new ClientException();

        $this->assertInstanceOf('Exception', $exception);
    }
}
