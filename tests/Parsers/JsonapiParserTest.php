<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Parsers\JsonapiParser;
use Swis\JsonApi\Client\Parsers\MetaParser;
use Swis\JsonApi\Client\Tests\AbstractTest;

class JsonapiParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function itConvertsDataToJsonapi()
    {
        $parser = new JsonapiParser(new MetaParser());
        $jsonapi = $parser->parse($this->getJsonapi());

        $this->assertInstanceOf(Jsonapi::class, $jsonapi);
        $this->assertEquals('1.0', $jsonapi->getVersion());

        $this->assertInstanceOf(Meta::class, $jsonapi->getMeta());
        $this->assertEquals(new Meta(['copyright' => 'Copyright 2015 Example Corp.']), $jsonapi->getMeta());
    }

    /**
     * @test
     * @dataProvider provideInvalidData
     *
     * @param mixed $invalidData
     */
    public function itThrowsWhenDataIsNotAnObject($invalidData)
    {
        $parser = new JsonapiParser($this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Jsonapi MUST be an object, "%s" given.', gettype($invalidData)));

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
     * @test
     * @dataProvider provideInvalidVersionJsonapi
     *
     * @param mixed $invalidJsonapi
     */
    public function itThrowsWhenVersionIsNotAString($invalidJsonapi)
    {
        $parser = new JsonapiParser($this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Jsonapi property "version" MUST be a string, "%s" given.', gettype($invalidJsonapi->version)));

        $parser->parse($invalidJsonapi);
    }

    public function provideInvalidVersionJsonapi(): array
    {
        return [
            [json_decode('{"version": 1}', false)],
            [json_decode('{"version": 1.5}', false)],
            [json_decode('{"version": false}', false)],
            [json_decode('{"version": null}', false)],
            [json_decode('{"version": []}', false)],
            [json_decode('{"version": {}}', false)],
        ];
    }

    /**
     * @return \stdClass
     */
    protected function getJsonapi()
    {
        $data = [
            'version' => '1.0',
            'meta' => [
                'copyright' => 'Copyright 2015 Example Corp.',
            ],
        ];

        return json_decode(json_encode($data), false);
    }
}
