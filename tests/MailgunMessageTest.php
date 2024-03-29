<?php

namespace Keruald\Mailgun\Tests;

use Keruald\Mailgun\MailgunMessage;

use PHPUnit\Framework\TestCase;
use stdClass;

class MailgunMessageTest extends TestCase {

    use WithMockHttpClient;

    /**
     * @var MailgunMessage
     */
    private $message;

    public function setUp() : void {
        $client = self::mockHttpClient();
        $this->message = new MailgunMessage($client, "https://api/msg", "0000");
    }

    public function testGet () {
        $this->assertEquals(
            json_decode(self::mockHttpClientResponseBody()),
            $this->message->get()
        );
    }

    public function testGetWhenMessageIsAlreadyCached () {
        $this->message->get();
        $this->assertEquals(
            json_decode(self::mockHttpClientResponseBody()),
            $this->message->get()
        );
    }

    public function testFetchThrowsExceptionWhenStatusCodeIsNot200 () {
        $this->expectException(\RuntimeException::class);
        $client = self::mockHttpClientWithCustomResponse(500, null);
        $message = new MailgunMessage($client, "https://api/msg", "0000");
        $message->get();
    }

    public function testLoadFromEventPayload () {
        $client = self::mockHttpClient();
        $payload = self::mockEventPayload();
        $message = MailgunMessage::loadFromEventPayload($client, $payload, "0000");
        $this->assertEquals(
            json_decode(self::mockHttpClientResponseBody()),
            $message->get()
        );
    }

    public function testLoadFromEventPayloadWithWrongPayload () {
        $this->expectException(\InvalidArgumentException::class);
        $client = self::mockHttpClient();
        $payload = new stdClass;
        MailgunMessage::loadFromEventPayload($client, $payload, "0000");
    }


}
