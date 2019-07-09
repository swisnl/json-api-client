<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Parsers\LinksParser;
use Swis\JsonApi\Client\Parsers\MetaParser;
use Swis\JsonApi\Client\Tests\AbstractTest;

class LinksParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_art4links_to_links()
    {
        $parser = new LinksParser(new MetaParser());
        $links = $parser->parse($this->getArt4Links()->asArray());

        $this->assertInstanceOf(Links::class, $links);
        $this->assertCount(3, $links->toArray());

        /** @var \Swis\JsonApi\Client\Link $link */
        $link = $links->self;
        $this->assertInstanceOf(Link::class, $link);
        $this->assertEquals('http://example.com/articles', $link->getHref());
        $this->assertInstanceOf(Meta::class, $link->getMeta());
        $this->assertEquals(new Meta(['copyright' => 'Copyright 2015 Example Corp.']), $link->getMeta());

        /** @var \Swis\JsonApi\Client\Link $link */
        $link = $links->next;
        $this->assertInstanceOf(Link::class, $link);
        $this->assertEquals('http://example.com/articles?page[offset]=2', $link->getHref());
        $this->assertNull($link->getMeta());

        /** @var \Swis\JsonApi\Client\Link $link */
        $link = $links->last;
        $this->assertInstanceOf(Link::class, $link);
        $this->assertEquals('http://example.com/articles?page[offset]=10', $link->getHref());
        $this->assertNull($link->getMeta());
    }

    /**
     * @return \Art4\JsonApiClient\ErrorCollection
     */
    protected function getArt4Links()
    {
        $links = [
            'links' => [
                'self' => [
                    'href' => 'http://example.com/articles',
                    'meta' => [
                        'copyright' => 'Copyright 2015 Example Corp.',
                    ],
                ],
                'next' => [
                    'href' => 'http://example.com/articles?page[offset]=2',
                ],
                'last' => 'http://example.com/articles?page[offset]=10',
            ],
            'data'  => [],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($links));

        return $jsonApiItem->get('links');
    }
}
