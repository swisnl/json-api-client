<?php

namespace Swis\JsonApi\Client\Tests\JsonApi;

use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Client\Error;
use Swis\JsonApi\Client\ErrorCollection;
use Swis\JsonApi\Client\ErrorSource;
use Swis\JsonApi\Client\JsonApi\ErrorsParser;
use Swis\JsonApi\Client\JsonApi\LinksParser;
use Swis\JsonApi\Client\JsonApi\MetaParser;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Tests\AbstractTest;

class ErrorsParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_art4errors_to_errors()
    {
        $parser = new ErrorsParser(new LinksParser(new MetaParser()), new MetaParser());
        $errorCollection = $parser->parse($this->getArt4ErrorCollection());

        $this->assertInstanceOf(ErrorCollection::class, $errorCollection);
        $this->assertEquals(2, $errorCollection->count());

        $errorCollection->each(
            function (Error $error) {
                $this->assertInstanceOf(Error::class, $error);
                $this->assertInstanceOf(Links::class, $error->getLinks());
                $this->assertInstanceOf(Meta::class, $error->getMeta());
                $this->assertInstanceOf(ErrorSource::class, $error->getSource());

                $this->assertEquals('http://example.com/docs/error/json_client_content_id_in_object_not_equal_to_id_parameter', $error->getLinks()['about']->getHref());
                $this->assertEquals('400', $error->getStatus());
                $this->assertEquals('json_client_content_id_in_object_not_equal_to_id_parameter', $error->getCode());
                $this->assertEquals('I refuse to save a sport with this id. ✟', $error->getTitle());
                $this->assertEquals("id is '666', id is '666'", $error->getDetail());
                $this->assertEquals('', $error->getSource()->getPointer());
                $this->assertEquals('666', $error->getSource()->getParameter());
                $this->assertEquals('Copyright 2015 Example Corp.', $error->getMeta()->copyright);
            }
        );

        $this->assertEquals(1, $errorCollection->first()->getId());
        $this->assertEquals(2, $errorCollection->get(1)->getId());
    }

    /**
     * @return \Art4\JsonApiClient\ErrorCollection
     */
    protected function getArt4ErrorCollection()
    {
        $errors = [
            'errors' => [
                [
                    'id'     => '1',
                    'links'  => [
                        'about' => 'http://example.com/docs/error/json_client_content_id_in_object_not_equal_to_id_parameter',
                    ],
                    'status' => '400',
                    'code'   => 'json_client_content_id_in_object_not_equal_to_id_parameter',
                    'title'  => 'I refuse to save a sport with this id. ✟',
                    'detail' => "id is '666', id is '666'",
                    'source' => [
                        'pointer'   => '',
                        'parameter' => '666',
                    ],
                    'meta' => [
                        'copyright' => 'Copyright 2015 Example Corp.',
                    ],
                ],
                [
                    'id'     => '2',
                    'links'  => [
                        'about' => [
                            'href' => 'http://example.com/docs/error/json_client_content_id_in_object_not_equal_to_id_parameter',
                            'meta' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                    'status' => '400',
                    'code'   => 'json_client_content_id_in_object_not_equal_to_id_parameter',
                    'title'  => 'I refuse to save a sport with this id. ✟',
                    'detail' => "id is '666', id is '666'",
                    'source' => [
                        'pointer'   => '',
                        'parameter' => '666',
                    ],
                    'meta' => [
                        'copyright' => 'Copyright 2015 Example Corp.',
                    ],
                ],
            ],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($errors));

        return $jsonApiItem->get('errors');
    }
}
