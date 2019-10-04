<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use Swis\JsonApi\Client\Error;
use Swis\JsonApi\Client\ErrorSource;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Parsers\ErrorParser;
use Swis\JsonApi\Client\Parsers\LinksParser;
use Swis\JsonApi\Client\Parsers\MetaParser;
use Swis\JsonApi\Client\Tests\AbstractTest;

class ErrorParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_data_to_error()
    {
        $parser = new ErrorParser(new LinksParser(new MetaParser()), new MetaParser());
        $error = $parser->parse($this->getError());

        $this->assertInstanceOf(Error::class, $error);
        $this->assertInstanceOf(Links::class, $error->getLinks());
        $this->assertInstanceOf(Meta::class, $error->getMeta());
        $this->assertInstanceOf(ErrorSource::class, $error->getSource());

        $this->assertEquals('1', $error->getId());
        $this->assertEquals('http://example.com/docs/error/json_client_content_id_in_object_not_equal_to_id_parameter', $error->getLinks()->about->getHref());
        $this->assertEquals('400', $error->getStatus());
        $this->assertEquals('json_client_content_id_in_object_not_equal_to_id_parameter', $error->getCode());
        $this->assertEquals('I refuse to save a sport with this id. ✟', $error->getTitle());
        $this->assertEquals("id is '666', id is '666'", $error->getDetail());
        $this->assertEquals('', $error->getSource()->getPointer());
        $this->assertEquals('666', $error->getSource()->getParameter());
        $this->assertEquals('Copyright 2015 Example Corp.', $error->getMeta()->copyright);
    }

    /**
     * @test
     * @dataProvider provideInvalidData
     *
     * @param mixed $invalidData
     */
    public function it_throws_when_data_is_not_an_object($invalidData)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Error MUST be an object, "%s" given.', gettype($invalidData)));

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
     * @dataProvider provideInvalidIdError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_id_is_not_a_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Error property "id" MUST be a string, "%s" given.', gettype($invalidError->id)));

        $parser->parse($invalidError);
    }

    public function provideInvalidIdError(): array
    {
        return [
            [json_decode('{"id": 1}', false)],
            [json_decode('{"id": 1.5}', false)],
            [json_decode('{"id": false}', false)],
            [json_decode('{"id": null}', false)],
            [json_decode('{"id": []}', false)],
            [json_decode('{"id": {}}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidStatusError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_status_is_not_a_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Error property "status" MUST be a string, "%s" given.', gettype($invalidError->status)));

        $parser->parse($invalidError);
    }

    public function provideInvalidStatusError(): array
    {
        return [
            [json_decode('{"status": 1}', false)],
            [json_decode('{"status": 1.5}', false)],
            [json_decode('{"status": false}', false)],
            [json_decode('{"status": null}', false)],
            [json_decode('{"status": []}', false)],
            [json_decode('{"status": {}}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidCodeError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_code_is_not_a_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Error property "code" MUST be a string, "%s" given.', gettype($invalidError->code)));

        $parser->parse($invalidError);
    }

    public function provideInvalidCodeError(): array
    {
        return [
            [json_decode('{"code": 1}', false)],
            [json_decode('{"code": 1.5}', false)],
            [json_decode('{"code": false}', false)],
            [json_decode('{"code": null}', false)],
            [json_decode('{"code": []}', false)],
            [json_decode('{"code": {}}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidTitleError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_title_is_not_a_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Error property "title" MUST be a string, "%s" given.', gettype($invalidError->title)));

        $parser->parse($invalidError);
    }

    public function provideInvalidTitleError(): array
    {
        return [
            [json_decode('{"title": 1}', false)],
            [json_decode('{"title": 1.5}', false)],
            [json_decode('{"title": false}', false)],
            [json_decode('{"title": null}', false)],
            [json_decode('{"title": []}', false)],
            [json_decode('{"title": {}}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidDetailError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_detail_is_not_a_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('Error property "detail" MUST be a string, "%s" given.', gettype($invalidError->detail)));

        $parser->parse($invalidError);
    }

    public function provideInvalidDetailError(): array
    {
        return [
            [json_decode('{"detail": 1}', false)],
            [json_decode('{"detail": 1.5}', false)],
            [json_decode('{"detail": false}', false)],
            [json_decode('{"detail": null}', false)],
            [json_decode('{"detail": []}', false)],
            [json_decode('{"detail": {}}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidErrorSourceError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_errorsource_is_not_an_object($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('ErrorSource MUST be an object, "%s" given.', gettype($invalidError->source)));

        $parser->parse($invalidError);
    }

    public function provideInvalidErrorSourceError(): array
    {
        return [
            [json_decode('{"source": 1}', false)],
            [json_decode('{"source": 1.5}', false)],
            [json_decode('{"source": false}', false)],
            [json_decode('{"source": null}', false)],
            [json_decode('{"source": []}', false)],
            [json_decode('{"source": "foo"}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidErrorSourcePointerError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_errorsource_pointer_is_not_an_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('ErrorSource property "pointer" MUST be a string, "%s" given.', gettype($invalidError->source->pointer)));

        $parser->parse($invalidError);
    }

    public function provideInvalidErrorSourcePointerError(): array
    {
        return [
            [json_decode('{"source": {"pointer": 1}}', false)],
            [json_decode('{"source": {"pointer": 1.5}}', false)],
            [json_decode('{"source": {"pointer": false}}', false)],
            [json_decode('{"source": {"pointer": null}}', false)],
            [json_decode('{"source": {"pointer": []}}', false)],
            [json_decode('{"source": {"pointer": {}}}', false)],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidErrorSourceParameterError
     *
     * @param mixed $invalidError
     */
    public function it_throws_when_errorsource_parameter_is_not_an_string($invalidError)
    {
        $parser = new ErrorParser($this->createMock(LinksParser::class), $this->createMock(MetaParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('ErrorSource property "parameter" MUST be a string, "%s" given.', gettype($invalidError->source->parameter)));

        $parser->parse($invalidError);
    }

    public function provideInvalidErrorSourceParameterError(): array
    {
        return [
            [json_decode('{"source": {"parameter": 1}}', false)],
            [json_decode('{"source": {"parameter": 1.5}}', false)],
            [json_decode('{"source": {"parameter": false}}', false)],
            [json_decode('{"source": {"parameter": null}}', false)],
            [json_decode('{"source": {"parameter": []}}', false)],
            [json_decode('{"source": {"parameter": {}}}', false)],
        ];
    }

    /**
     * @return \stdClass
     */
    protected function getError()
    {
        $data = [
            'id' => '1',
            'links' => [
                'about' => [
                    'href' => 'http://example.com/docs/error/json_client_content_id_in_object_not_equal_to_id_parameter',
                    'meta' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'status' => '400',
            'code' => 'json_client_content_id_in_object_not_equal_to_id_parameter',
            'title' => 'I refuse to save a sport with this id. ✟',
            'detail' => "id is '666', id is '666'",
            'source' => [
                'pointer' => '',
                'parameter' => '666',
            ],
            'meta' => [
                'copyright' => 'Copyright 2015 Example Corp.',
            ],
        ];

        return json_decode(json_encode($data), false);
    }
}
