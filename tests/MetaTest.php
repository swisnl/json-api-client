<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Meta;

class MetaTest extends TestCase
{
    public function testGet()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals('bar', $meta->foo);
        $this->assertNull($meta->other);
    }

    public function testOffsetGet()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals('bar', $meta['foo']);
        $this->assertNull($meta['other']);
    }

    public function testIsset()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertTrue(isset($meta->foo));
        $this->assertFalse(isset($meta->other));
    }

    public function testOffsetExists()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertTrue(isset($meta['foo']));
        $this->assertFalse(isset($meta['other']));
    }

    public function testSet()
    {
        $meta = new Meta([]);

        $meta->foo = 'bar';

        $this->assertEquals('bar', $meta->foo);
    }

    public function testOffsetSet()
    {
        $meta = new Meta([]);

        $meta['foo'] = 'bar';

        $this->assertEquals('bar', $meta['foo']);
    }

    public function testUnset()
    {
        $meta = new Meta(['foo' => 'bar']);

        unset($meta->foo);

        $this->assertNull($meta->foo);
    }

    public function testOffsetUnset()
    {
        $meta = new Meta(['foo' => 'bar']);

        unset($meta['foo']);

        $this->assertNull($meta['foo']);
    }

    public function testToArray()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'],
            $meta->toArray()
        );
    }

    public function testToJson()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals(
            '{"foo":"bar"}',
            $meta->toJson()
        );
    }

    public function testJsonSerialize()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'],
            $meta->toArray()
        );
    }
}
