<?php

namespace Swis\JsonApi\Client\Tests\JsonApi;

use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Client\JsonApi\MetaParser;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Tests\AbstractTest;

class MetaParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_art4meta_to_meta()
    {
        $parser = new MetaParser();
        $meta = $parser->parse($this->getArt4Meta());

        $this->assertInstanceOf(Meta::class, $meta);
        $this->assertCount(1, $meta->toArray());
        $this->assertEquals(new Meta(['copyright' => 'Copyright 2015 Example Corp.']), $meta);
    }

    /**
     * @return \Art4\JsonApiClient\Meta
     */
    protected function getArt4Meta()
    {
        $meta = [
            'meta' => [
                'copyright' => 'Copyright 2015 Example Corp.',
            ],
            'data' => [],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($meta));

        return $jsonApiItem->get('meta');
    }
}
