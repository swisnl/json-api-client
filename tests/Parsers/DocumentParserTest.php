<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\Error;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\ItemDocument;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Parsers\DocumentParser;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;
use Swis\JsonApi\Client\TypeMapper;

class DocumentParserTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateAnInstanceUsingAFactoryMethod()
    {
        $this->assertInstanceOf(DocumentParser::class, DocumentParser::create());
    }

    /**
     * @test
     */
    public function itConvertsJsondocumentToDocument()
    {
        $parser = DocumentParser::create();
        $document = $parser->parse(
            json_encode(
                [
                    'data' => [],
                ]
            )
        );

        $this->assertInstanceOf(Document::class, $document);
    }

    /**
     * @test
     */
    public function itThrowsWhenJsonIsNotValid()
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Unable to parse JSON data: Malformed UTF-8 characters, possibly incorrectly encoded');

        $parser->parse("\x80");
    }

    /**
     * @test
     * @dataProvider provideInvalidJson
     *
     * @param string $invalidJson
     */
    public function itThrowsWhenJsonIsNotAJsonapiDocument(string $invalidJson)
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Document MUST be an object, "%s" given.', gettype(json_decode($invalidJson, false))));

        $parser->parse($invalidJson);
    }

    public function provideInvalidJson(): array
    {
        return [
            [json_encode(null)],
            [json_encode(1)],
            [json_encode(1.5)],
            [json_encode(false)],
            [json_encode('Foo bar')],
            [json_encode(['Foo bar'])],
        ];
    }

    /**
     * @test
     */
    public function itThrowsWhenDataErrorsAndMetaAreMissing()
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Document MUST contain at least one of the following properties: `data`, `errors`, `meta`.');

        $parser->parse(json_encode(new \stdClass()));
    }

    /**
     * @test
     */
    public function itThrowsWhenBothDataAndErrorsArePresent()
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The properties `data` and `errors` MUST NOT coexist in Document.');

        $parser->parse('{"data": [], "errors": []}');
    }

    /**
     * @test
     */
    public function itThrowsWhenIncludedIsPresentButDataIsNot()
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('If Document does not contain a `data` property, the `included` property MUST NOT be present either.');

        $parser->parse('{"included": [], "errors": []}');
    }

    /**
     * @test
     * @dataProvider provideInvalidData
     *
     * @param mixed $invalidData
     */
    public function itThrowsWhenDataIsNotAnArrayObjectOrNull($invalidData)
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Document property "data" MUST be null, an array or an object, "%s" given.', gettype(json_decode($invalidData, false)->data)));

        $parser->parse($invalidData);
    }

    public function provideInvalidData(): array
    {
        return [
            ['{"data": 1}'],
            ['{"data": 1.5}'],
            ['{"data": false}'],
            ['{"data": "foo"}'],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidIncluded
     *
     * @param mixed $invalidIncluded
     */
    public function itThrowsWhenIncludedIsNotAnArray($invalidIncluded)
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Document property "included" MUST be an array, "%s" given.', gettype(json_decode($invalidIncluded, false)->included)));

        $parser->parse($invalidIncluded);
    }

    public function provideInvalidIncluded(): array
    {
        return [
            ['{"data": [], "included": null}'],
            ['{"data": [], "included": 1}'],
            ['{"data": [], "included": 1.5}'],
            ['{"data": [], "included": false}'],
            ['{"data": [], "included": "foo"}'],
            ['{"data": [], "included": {}}'],
        ];
    }

    /**
     * @test
     */
    public function itThrowsWhenItFindsDuplicateResources()
    {
        $parser = DocumentParser::create();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Resources MUST be unique based on their `type` and `id`, 1 duplicate(s) found.');

        $parser->parse(
            json_encode(
                [
                    'data' => [
                        [
                            'type' => 'master',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'master',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                ]
            )
        );
    }

    /**
     * @test
     */
    public function itParsesAResourceDocument()
    {
        $parser = DocumentParser::create();
        $document = $parser->parse(
            json_encode(
                [
                    'data' => [
                        'type' => 'master',
                        'id' => '1',
                        'attributes' => [
                            'foo' => 'bar',
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(ItemDocument::class, $document);
        $this->assertInstanceOf(ItemInterface::class, $document->getData());
        $this->assertEquals('master', $document->getData()->getType());
        $this->assertEquals('1', $document->getData()->getId());
    }

    /**
     * @test
     */
    public function itParsesAResourceCollectionDocument()
    {
        $parser = DocumentParser::create();
        $document = $parser->parse(
            json_encode(
                [
                    'data' => [
                        [
                            'type' => 'master',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(CollectionDocument::class, $document);
        $this->assertInstanceOf(Collection::class, $document->getData());
        $this->assertCount(1, $document->getData());
        $this->assertEquals('master', $document->getData()->get(0)->getType());
        $this->assertEquals('1', $document->getData()->get(0)->getId());
    }

    /**
     * @test
     */
    public function itParsesADocumentWithoutData()
    {
        $parser = DocumentParser::create();
        $document = $parser->parse(
            json_encode(
                [
                    'meta' => [
                        'foo' => 'bar',
                    ],
                ]
            )
        );

        $this->assertInstanceOf(Document::class, $document);
    }

    /**
     * @test
     */
    public function itParsesIncluded()
    {
        $parser = DocumentParser::create();
        $document = $parser->parse(
            json_encode(
                [
                    'data' => [],
                    'included' => [
                        [
                            'type' => 'master',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(CollectionDocument::class, $document);
        $this->assertInstanceOf(Collection::class, $document->getIncluded());
        $this->assertCount(1, $document->getIncluded());
        $this->assertEquals('master', $document->getIncluded()->get(0)->getType());
        $this->assertEquals('1', $document->getIncluded()->get(0)->getId());
    }

    /**
     * @test
     */
    public function itLinksSingularRelationsToItemsFromIncluded()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('master', MasterItem::class);
        $typeMapper->setMapping('child', ChildItem::class);
        $parser = DocumentParser::create($typeMapper);

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [
                        'type' => 'master',
                        'id' => '1',
                        'attributes' => [
                            'foo' => 'bar',
                        ],
                        'relationships' => [
                            'child' => [
                                'data' => [
                                    'type' => 'child',
                                    'id' => '1',
                                ],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'child',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'baz',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(MasterItem::class, $document->getData());
        $this->assertInstanceOf(ChildItem::class, $document->getData()->child()->getIncluded());
        $this->assertSame($document->getIncluded()->get(0), $document->getData()->child()->getIncluded());
    }

    /**
     * @test
     */
    public function itDoesNotLinkEmptySingularRelations()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('master', MasterItem::class);
        $typeMapper->setMapping('child', ChildItem::class);
        $parser = DocumentParser::create($typeMapper);

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [
                        'type' => 'master',
                        'id' => '1',
                        'attributes' => [
                            'foo' => 'bar',
                        ],
                        'relationships' => [
                            'child' => [
                                'data' => null,
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'child',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'baz',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(MasterItem::class, $document->getData());
        $this->assertNull($document->getData()->child()->getIncluded());
        $this->assertInstanceOf(ChildItem::class, $document->getIncluded()->get(0));
    }

    /**
     * @test
     */
    public function itLinksPluralRelationsToItemsFromIncluded()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('master', MasterItem::class);
        $typeMapper->setMapping('child', ChildItem::class);
        $parser = DocumentParser::create($typeMapper);

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [
                        'type' => 'master',
                        'id' => '1',
                        'attributes' => [
                            'foo' => 'bar',
                        ],
                        'relationships' => [
                            'children' => [
                                'data' => [
                                    [
                                        'type' => 'child',
                                        'id' => '1',
                                    ],
                                    [
                                        'type' => 'child',
                                        'id' => '2',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'child',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'baz',
                            ],
                        ],
                        [
                            'type' => 'child',
                            'id' => '2',
                            'attributes' => [
                                'foo' => 'baz',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(MasterItem::class, $document->getData());
        $this->assertInstanceOf(Collection::class, $document->getData()->children()->getIncluded());
        $this->assertInstanceOf(ChildItem::class, $document->getData()->children()->getIncluded()->get(0));
        $this->assertSame($document->getIncluded()->get(0), $document->getData()->children()->getIncluded()->get(0));
        $this->assertSame($document->getIncluded()->get(1), $document->getData()->children()->getIncluded()->get(1));
    }

    /**
     * @test
     */
    public function itDoesNotLinkEmptyPluralRelations()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('master', MasterItem::class);
        $typeMapper->setMapping('child', ChildItem::class);
        $parser = DocumentParser::create($typeMapper);

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [
                        'type' => 'master',
                        'id' => '1',
                        'attributes' => [
                            'foo' => 'bar',
                        ],
                        'relationships' => [
                            'children' => [
                                'data' => [],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'child',
                            'id' => '1',
                            'attributes' => [
                                'foo' => 'baz',
                            ],
                        ],
                        [
                            'type' => 'child',
                            'id' => '2',
                            'attributes' => [
                                'foo' => 'baz',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertInstanceOf(MasterItem::class, $document->getData());
        $this->assertInstanceOf(Collection::class, $document->getData()->children()->getIncluded());
        $this->assertEmpty($document->getData()->children()->getIncluded());
        $this->assertInstanceOf(ChildItem::class, $document->getIncluded()->get(0));
        $this->assertInstanceOf(ChildItem::class, $document->getIncluded()->get(1));
    }

    /**
     * @test
     */
    public function itParsesLinks()
    {
        $parser = DocumentParser::create();

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [],
                    'links' => [
                        'self' => 'http://example.com/blogs',
                    ],
                ]
            )
        );

        static::assertInstanceOf(Links::class, $document->getLinks());

        static::assertEquals(new Links(['self' => new Link('http://example.com/blogs')]), $document->getLinks());
    }

    /**
     * @test
     */
    public function itParsesErrors()
    {
        $parser = DocumentParser::create();

        $document = $parser->parse(
            json_encode(
                [
                    'errors' => [
                        [
                            'id' => '1',
                            'code' => 'foo_bar',
                        ],
                    ],
                ]
            )
        );

        static::assertInstanceOf(ErrorCollection::class, $document->getErrors());

        static::assertEquals(new ErrorCollection([new Error('1', null, null, 'foo_bar')]), $document->getErrors());
    }

    /**
     * @test
     */
    public function itParsesMeta()
    {
        $parser = DocumentParser::create();

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [],
                    'meta' => [
                        'foo' => 'bar',
                    ],
                ]
            )
        );

        static::assertInstanceOf(Meta::class, $document->getMeta());

        static::assertEquals(new Meta(['foo' => 'bar']), $document->getMeta());
    }

    /**
     * @test
     */
    public function itParsesJsonapi()
    {
        $parser = DocumentParser::create();

        $document = $parser->parse(
            json_encode(
                [
                    'data' => [],
                    'jsonapi' => [
                        'version' => '1.0',
                    ],
                ]
            )
        );

        static::assertInstanceOf(Jsonapi::class, $document->getJsonapi());

        static::assertEquals(new Jsonapi('1.0'), $document->getJsonapi());
    }
}
