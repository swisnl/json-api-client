<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;

class LinksTest extends TestCase
{
    public function test_get()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals($link, $links->self);
        $this->assertNull($links->related);
    }

    public function test_offset_get()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals($link, $links['self']);
        $this->assertNull($links['related']);
    }

    public function test_isset()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertTrue(isset($links->self));
        $this->assertFalse(isset($links->related));
    }

    public function test_offset_exists()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertTrue(isset($links['self']));
        $this->assertFalse(isset($links['related']));
    }

    public function test_set()
    {
        $link = new Link('http://example.com/self');
        $links = new Links([]);

        $links->self = $link;

        $this->assertEquals($link, $links->self);
    }

    public function test_offset_set()
    {
        $link = new Link('http://example.com/self');
        $links = new Links([]);

        $links['self'] = $link;

        $this->assertEquals($link, $links['self']);
    }

    public function test_unset()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        unset($links->self);

        $this->assertNull($links->self);
    }

    public function test_offset_unset()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        unset($links['self']);

        $this->assertNull($links['self']);
    }

    public function test_to_array()
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

    public function test_to_json()
    {
        $link = new Link('http://example.com/self');
        $links = new Links(['self' => $link]);

        $this->assertEquals(
            '{"self":{"href":"http:\/\/example.com\/self"}}',
            $links->toJson()
        );
    }

    public function test_json_serialize()
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
