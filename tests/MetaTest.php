<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Meta;

class MetaTest extends TestCase
{
    public function test_get()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals('bar', $meta->foo);
        $this->assertNull($meta->other);
    }

    public function test_offset_get()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals('bar', $meta['foo']);
        $this->assertNull($meta['other']);
    }

    public function test_isset()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertTrue(isset($meta->foo));
        $this->assertFalse(isset($meta->other));
    }

    public function test_offset_exists()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertTrue(isset($meta['foo']));
        $this->assertFalse(isset($meta['other']));
    }

    public function test_set()
    {
        $meta = new Meta([]);

        $meta->foo = 'bar';

        $this->assertEquals('bar', $meta->foo);
    }

    public function test_offset_set()
    {
        $meta = new Meta([]);

        $meta['foo'] = 'bar';

        $this->assertEquals('bar', $meta['foo']);
    }

    public function test_unset()
    {
        $meta = new Meta(['foo' => 'bar']);

        unset($meta->foo);

        $this->assertNull($meta->foo);
    }

    public function test_offset_unset()
    {
        $meta = new Meta(['foo' => 'bar']);

        unset($meta['foo']);

        $this->assertNull($meta['foo']);
    }

    public function test_to_array()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'],
            $meta->toArray()
        );
    }

    public function test_to_json()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals(
            '{"foo":"bar"}',
            $meta->toJson()
        );
    }

    public function test_json_serialize()
    {
        $meta = new Meta(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'],
            $meta->toArray()
        );
    }
}
