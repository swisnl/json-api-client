<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Parsers\MetaParser;
use Swis\JsonApi\Client\Tests\AbstractTest;

class MetaParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_data_to_meta()
    {
        $parser = new MetaParser();
        $meta = $parser->parse($this->getMeta());

        $this->assertInstanceOf(Meta::class, $meta);
        $this->assertCount(1, $meta->toArray());
        $this->assertEquals(new Meta(['copyright' => 'Copyright 2015 Example Corp.']), $meta);
    }

    /**
     * @test
     * @dataProvider provideInvalidData
     *
     * @param mixed $invalidData
     */
    public function it_throws_when_data_is_not_an_object($invalidData)
    {
        $parser = new MetaParser();

        $this->expectException(ValidationException::class);

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
