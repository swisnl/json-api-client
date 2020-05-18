<?php

namespace Swis\JsonApi\Client\Tests;

use GuzzleHttp\Psr7\Response;
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
    public function it_can_get_and_set_a_response()
    {
        $document = new Document();
        $response = new Response();

        $document->setResponse($response);

        $this->assertSame($response, $document->getResponse());
    }

    /**
     * @test
     */
    public function it_can_get_and_set_errors()
    {
        $document = new Document();
        $errors = new ErrorCollection();

        $document->setErrors($errors);

        $this->assertSame($errors, $document->getErrors());
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_indicating_if_it_has_errors()
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
    public function it_can_get_and_set_included()
    {
        $document = new Document();
        $included = new Collection();

        $document->setIncluded($included);

        $this->assertSame($included, $document->getIncluded());
    }

    /**
     * @test
     */
    public function it_can_get_and_set_jsonapi()
    {
        $document = new Document();
        $jsonApi = new Jsonapi();

        $document->setJsonapi($jsonApi);

        $this->assertSame($jsonApi, $document->getJsonapi());
    }

    /**
     * @test
     */
    public function it_can_get_and_set_an_item_as_data()
    {
        $document = new Document();
        $data = new Item();

        $document->setData($data);

        $this->assertSame($data, $document->getData());
    }

    /**
     * @test
     */
    public function it_can_get_and_set_a_collection_as_data()
    {
        $document = new Document();
        $data = new Collection([new Item()]);

        $document->setData($data);

        $this->assertSame($data, $document->getData());
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
                        'href' => 'http://example.com/articles',
                        'meta' => [
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
                'data' => [
                    'type' => 'articles',
                    'id' => 1,
                    'attributes' => [
                        'title' => 'JSON:API paints my bikeshed!',
                    ],
                ],
                'included' => [
                    [
                        'type' => 'people',
                        'id' => 9,
                        'attributes' => [
                            'firstName' => 'Dan',
                            'lastName' => 'Gebhardt',
                            'twitter' => 'dgeb',
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
                    'meta' => [
                        'copyright' => 'Copyright 2015 Example Corp.',
                    ],
                ],
            ],
            $document->toArray()
        );
    }

    /**
     * @test
     */
    public function it_serializes_empty_members_as_empty_objects()
    {
        $document = new Document();

        $this->assertEquals('{}', json_encode($document));

        $document->setData(new Collection());
        $document->setErrors(new ErrorCollection());
        $document->setIncluded(new Collection());
        $document->setJsonapi(new Jsonapi());
        $document->setLinks(new Links([]));
        $document->setMeta(new Meta([]));

        $this->assertEquals('{"links":{},"data":[],"meta":{},"jsonapi":{}}', json_encode($document));
    }
}
