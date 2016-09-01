<?php

namespace Keruald\Mailgun\Tests;

use Keruald\Mailgun\MailgunMessage;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

class MailgunMessageTest extends TestCase {

    use WithMockHttpClient;

    /**
     * @var MailgunMessage
     */
    private $message;

    public function setUp() {
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

    /**
     * @expectedException \RuntimeException
     */
    public function testFetchThrowsExceptionWhenStatusCodeIsNot200 () {
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadFromEventPayloadWithWrongPayload () {
        $client = self::mockHttpClient();
        $payload = new stdClass;
        MailgunMessage::loadFromEventPayload($client, $payload, "0000");
    }


}
