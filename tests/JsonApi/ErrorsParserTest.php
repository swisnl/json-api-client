<?php

class ErrorsParserTest extends AbstractTest
{
    /** @var \Swis\JsonApi\JsonApi\ErrorsParser */
    public static $parser;

    public static function setUpBeforeClass()
    {
        self::$parser = new \Swis\JsonApi\JsonApi\ErrorsParser();
    }

    /** @test */
    public function it_converts_jsonapierrorcollection_to_errorcollection()
    {
        $errorCollection = self::$parser->parse($this->getValidJsonApiErrorCollection());

        $this->assertInstanceOf(\Swis\JsonApi\Errors\ErrorCollection::class, $errorCollection);
        $this->assertEquals(2, $errorCollection->count());

        $errorCollection->each(
            function (\Swis\JsonApi\Errors\Error $error) {
                $this->assertInstanceOf(\Swis\JsonApi\Errors\Error::class, $error);
                $this->assertInstanceOf(\Swis\JsonApi\Errors\ErrorSource::class, $error->getSource());

                $this->assertEquals('400', $error->getStatus());
                $this->assertEquals('json_client_content_id_in_object_not_equal_to_id_parameter', $error->getCode());
                $this->assertEquals('I refuse to save a sport with this id. ✟', $error->getTitle());
                $this->assertEquals("id is '666', id is '666'", $error->getDetail());
                $this->assertEquals('', $error->getSource()->getPointer());
                $this->assertEquals('666', $error->getSource()->getParameter());
            }
        );

        $this->assertEquals(1, $errorCollection->first()->getId());
        $this->assertEquals(2, $errorCollection->get(1)->getId());
    }

    /**
     * @return \Art4\JsonApiClient\ErrorCollection
     */
    protected function getValidJsonApiErrorCollection()
    {
        $errors = [
            'errors' => [
                    [
                        'id'     => '1',
                        'status' => '400',
                        'code'   => 'json_client_content_id_in_object_not_equal_to_id_parameter',
                        'title'  => 'I refuse to save a sport with this id. ✟',
                        'detail' => "id is '666', id is '666'",
                        'source' => [
                                'pointer'   => '',
                                'parameter' => '666',
                            ],
                    ],
                    [
                        'id'     => '2',
                        'status' => '400',
                        'code'   => 'json_client_content_id_in_object_not_equal_to_id_parameter',
                        'title'  => 'I refuse to save a sport with this id. ✟',
                        'detail' => "id is '666', id is '666'",
                        'source' => [
                                'pointer'   => '',
                                'parameter' => '666',
                            ],
                    ],
                ],
        ];

        $manager = new \Art4\JsonApiClient\Utils\Manager();
        $jsonApiItem = $manager->parse(json_encode($errors));

        return $jsonApiItem->get('errors');
    }

    /**
     * @return \Art4\JsonApiClient\ErrorCollection
     */
    protected function getInValidJsonApiErrorCollection()
    {
        $errors = [
            'errors' => [
                    [
                        'id'     => '1',
                        'status' => '400',
                        'code'   => 'json_client_content_id_in_object_not_equal_to_id_parameter',
                        'title'  => 'I refuse to save a sport with this id. ✟',
                        'detail' => "id is '666', id is '666'",
                        'source' => [
                                'pointer'   => '',
                                'parameter' => '666',
                            ],
                    ],
                ],
        ];

        $manager = new \Art4\JsonApiClient\Utils\Manager();
        $jsonApiItem = $manager->parse(json_encode($errors));

        return $jsonApiItem->get('errors');
    }
}
