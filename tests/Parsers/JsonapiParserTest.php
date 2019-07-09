<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use Art4\JsonApiClient\Utils\Manager;
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
    public function it_converts_art4jsonapi_to_jsonapi()
    {
        $parser = new JsonapiParser(new MetaParser());
        $jsonapi = $parser->parse($this->getArt4Jsonapi());

        $this->assertInstanceOf(Jsonapi::class, $jsonapi);
        $this->assertEquals('1.0', $jsonapi->getVersion());

        $this->assertInstanceOf(Meta::class, $jsonapi->getMeta());
        $this->assertEquals(new Meta(['copyright' => 'Copyright 2015 Example Corp.']), $jsonapi->getMeta());
    }

    /**
     * @return \Art4\JsonApiClient\Jsonapi
     */
    protected function getArt4Jsonapi()
    {
        $jsonapi = [
            'jsonapi' => [
                'version' => '1.0',
                'meta'    => [
                    'copyright' => 'Copyright 2015 Example Corp.',
                ],
            ],
            'data'    => [],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($jsonapi));

        return $jsonApiItem->get('jsonapi');
    }
}
