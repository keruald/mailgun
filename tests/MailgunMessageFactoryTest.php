<?php

namespace Keruald\Mailgun\Tests;

use Keruald\Mailgun\MailgunMessageFactory;
use Keruald\Mailgun\MailgunMessage;

use PHPUnit\Framework\TestCase;

use stdClass;

class MailgunMessageFactoryTest extends TestCase {

    use WithMockHttpClient;

    /**
     * @var \Keruald\Mailgun\MailgunMessageFactory
     */
    private $factory;

    public function setUp () : void {
        $client = self::mockHttpClient();
        $this->factory = new MailgunMessageFactory($client, "0000");
    }

    public function testGetMessage () {
        $message = $this->factory->getMessage("http://api/somemessage");
        $this->assertInstanceOf(MailgunMessage::class, $message);
    }

    public function testGetMessageFromPayload () {
        $payload = self::mockEventPayload();
        $message = $this->factory->getMessageFromPayload($payload);
        $this->assertInstanceOf(MailgunMessage::class, $message);
    }

    public function testGetMessageFromPayloadThrowsExceptionWhenPayloadDoesNotContainUrlInformation () {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->getMessageFromPayload(new stdClass);
    }

    public function testFetchMessage () {
        $message = $this->factory->fetchMessage("http://api/somemessage");
        $this->assertInstanceOf("stdClass", $message);
    }

    public function testFetchMessageFromPayload () {
        $payload = self::mockEventPayload();
        $message = $this->factory->fetchMessageFromPayload($payload);
        $this->assertInstanceOf("stdClass", $message);
    }

}
