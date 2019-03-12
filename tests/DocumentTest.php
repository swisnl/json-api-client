<?php

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function it_knows_if_hasErrors()
    {
        $document = new Document();
        $this->assertEquals($document->isSuccess(), true);
        $this->assertEquals($document->hasErrors(), false);

        $document->setErrors(
          new ErrorCollection(
              [
                  ['id' => 'error-1'],
              ]
          )
      );
        $this->assertEquals($document->hasErrors(), true);
        $this->assertEquals($document->isSuccess(), false);
    }

    /**
     * @test
     */
    public function it_returns_only_filled_properties_in_toArray()
    {
        $document = new Document();

        $this->assertEquals([], $document->toArray());

        $document->setLinks(
            new Links(
                [
                    'self' => new Link(
                        'http://example.com/articles',
                        new Meta(
                            [
                                'copyright' => 'Copyright 2015 Example Corp.',
                            ]
                        )
                    ),
                    'next' => new Link('http://example.com/articles?page[offset]=2'),
                    'last' => new Link('http://example.com/articles?page[offset]=10'),
                ]
            )
        );
        $document->setData(
            (new Item(['title' => 'JSON:API paints my bikeshed!']))
                ->setType('articles')
                ->setId(1)
        );
        $document->setIncluded(
            new Collection(
                [
                    (new Item(['firstName' => 'Dan', 'lastName' => 'Gebhardt', 'twitter' => 'dgeb']))
                        ->setType('people')
                        ->setId(9),
                ]
            )
        );
        $document->setMeta(
            new Meta(
                [
                    'copyright' => 'Copyright 2015 Example Corp.',
                ]
            )
        );
        $document->setErrors(
            new ErrorCollection(
                [
                    ['id' => 'error-1'],
                ]
            )
        );
        $document->setJsonapi(
            new Jsonapi(
                '1.0',
                new Meta(
                    [
                        'copyright' => 'Copyright 2015 Example Corp.',
                    ]
                )
            )
        );

        $this->assertEquals(
            [
                'links' => [
                    'self' => [
                        'href'    => 'http://example.com/articles',
                        'meta'    => [
                            'copyright' => 'Copyright 2015 Example Corp.',
                        ],
                    ],
                    'next' => [
                        'href' => 'http://example.com/articles?page[offset]=2',
                    ],
                    'last' => [
                        'href' => 'http://example.com/articles?page[offset]=10',
                    ],
                ],
                'data'     => [
                    'type'       => 'articles',
                    'id'         => 1,
                    'attributes' => [
                        'title' => 'JSON:API paints my bikeshed!',
                    ],
                ],
                'included' => [
                    [
                        'type'       => 'people',
                        'id'         => 9,
                        'attributes' => [
                            'firstName' => 'Dan',
                            'lastName'  => 'Gebhardt',
                            'twitter'   => 'dgeb',
                        ],
                    ],
                ],
                'meta' => [
                    'copyright' => 'Copyright 2015 Example Corp.',
                ],
                'errors' => [
                    [
                        'id' => 'error-1',
                    ],
                ],
                'jsonapi' => [
                    'version' => '1.0',
                    'meta'    => [
                        'copyright' => 'Copyright 2015 Example Corp.',
                    ],
                ],
            ],
            $document->toArray()
        );
    }
}
