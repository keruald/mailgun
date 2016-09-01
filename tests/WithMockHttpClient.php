<?php

namespace Keruald\Mailgun\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

trait WithMockHttpClient {

    /**
     * @return \GuzzleHttp\
     */
    public function mockHttpClient () {
        $body = $this->mockHttpClientResponseBody();
        return self::mockHttpClientWithCustomResponse(200, $body);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function mockHttpClientWithCustomResponse ($code, $body) {
        $handler = self::getCustomMockHttpClientHandler($code, $body);
        return new Client(['handler' => $handler]);
    }

    /**
     * @return stdClass
     */
    public function mockEventPayload () {
        return json_decode(file_get_contents(__DIR__ . '/payload.json'));
    }

    ///
    /// Mock helper methods
    ///

    /**
     * @return \GuzzleHttp\HandlerStack
     */
    protected static function getCustomMockHttpClientHandler ($code, $body, $headers = []) {
        return HandlerStack::create(new MockHandler([
            new Response($code, $headers, $body),
        ]));
    }

    /**
     * @return string
     */
    protected static function mockHttpClientResponseBody () {
        return file_get_contents(__DIR__ . '/response.json');
    }

}
