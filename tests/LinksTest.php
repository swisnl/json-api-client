<?php

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;

class LinksTest extends TestCase
{
    public function test__get()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals($link, $links->self);
        $this->assertNull($links->related);
    }

    public function testOffsetGet()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals($link, $links['self']);
        $this->assertNull($links['related']);
    }

    public function test__isset()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertTrue(isset($links->self));
        $this->assertFalse(isset($links->related));
    }

    public function testOffsetExists()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertTrue(isset($links['self']));
        $this->assertFalse(isset($links['related']));
    }

    public function test__set()
    {
        $link = new Link('http://example.com/self');
        $links = new Links([]);

        $links->self = $link;

        $this->assertEquals($link, $links->self);
    }

    public function testOffsetSet()
    {
        $link = new Link('http://example.com/self');
        $links = new Links([]);

        $links['self'] = $link;

        $this->assertEquals($link, $links['self']);
    }

    public function test__unset()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        unset($links->self);

        $this->assertNull($links->self);
    }

    public function testOffsetUnset()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        unset($links['self']);

        $this->assertNull($links['self']);
    }

    public function testToArray()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals(
            [
                'self' => [
                    'href' => 'http://example.com/self',
                ],
            ],
            $links->toArray()
        );
    }

    public function testToJson()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals(
            '{"self":{"href":"http:\/\/example.com\/self"}}',
            $links->toJson()
        );
    }

    public function testJsonSerialize()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals(
            [
                'self' => [
                    'href' => 'http://example.com/self',
                ],
            ],
            $links->toArray()
        );
    }
}
