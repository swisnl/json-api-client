<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Parsers\MetaParser;

class MetaParserTest extends TestCase
{
    /**
     * @test
     */
    public function itConvertsDataToMeta()
    {
        $parser = new MetaParser();
        $meta = $parser->parse($this->getMeta());

        $this->assertInstanceOf(Meta::class, $meta);
        $this->assertCount(1, $meta->toArray());
        $this->assertEquals(new Meta(['copyright' => 'Copyright 2015 Example Corp.']), $meta);
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidData
     *
     * @param mixed $invalidData
     */
    public function itThrowsWhenDataIsNotAnObject($invalidData)
    {
        $parser = new MetaParser();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Meta MUST be an object, "%s" given.', gettype($invalidData)));

        $parser->parse($invalidData);
    }

    public function provideInvalidData(): array
    {
        return [
            [1],
            [1.5],
            [false],
            [null],
            ['foo'],
            [[]],
        ];
    }

    /**
     * @return \stdClass
     */
    protected function getMeta()
    {
        $data = [
            'copyright' => 'Copyright 2015 Example Corp.',
        ];

        return json_decode(json_encode($data), false);
    }
}
