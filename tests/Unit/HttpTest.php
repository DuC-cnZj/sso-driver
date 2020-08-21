<?php


namespace DucCnzj\Tests\Unit;


use DucCnzj\Sso\Http;
use DucCnzj\Tests\TestCase;

class HttpTest extends TestCase
{
    public function testInstance()
    {
        $client = Http::instance();
        $this->assertSame($client, Http::instance());
    }
}