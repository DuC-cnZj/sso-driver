<?php


namespace DucCnzj\Tests\Unit;


use DucCnzj\Sso\HttpClient;
use DucCnzj\Tests\TestCase;

class HttpTest extends TestCase
{
    public function testInstance()
    {
        $client = HttpClient::instance();
        $this->assertSame($client, HttpClient::instance());
    }
}