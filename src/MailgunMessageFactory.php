<?php

namespace Keruald\Mailgun;

use GuzzleHttp\ClientInterface;

use stdClass;

/**
 * Allows to build several MailgunMessage instances
 * with the same HTTP client and API key.
 */
class MailgunMessageFactory {

    ///
    /// Private members, to pass to MailgunMessage instances
    ///

    /**
     * @var string
     */
    private $key;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of the MailgunMessageFactory object.
     *
     * @param \GuzzleHttp\ClientInterface $client HTTP client
     * @param string $key The API key to use to fetch the message.
     */
    public function __construct (ClientInterface $client, $key) {
        $this->client = $client;
        $this->key = $key;
    }

    ///
    /// Builder
    ///

    /**
     * @param string $url The Mailgun URL of the message to fetch.
     * @return MailgunMessage
     */
    public function getMessage ($url) {
        return new MailgunMessage($this->client, $url, $this->key);
    }

    /**
     * Gets a JSON representation of a mail.
     *
     * @param string $url The Mailgun URL of the message to fetch.
     * @return object
     */
    public function fetchMessage ($url) {
        return $this->getMessage($url)->get();
    }

    /**
     * @param stdClass $payload The payload fired by MailGun routing API
     * @return MailgunMessage
     */
    public function getMessageFromPayload (stdClass $payload) {
        return MailgunMessage::loadFromEventPayload(
            $this->client, $payload, $this->key
        );
    }

    /**
     * Gets a JSON representation of a mail.
     *
     * @param stdClass $payload The payload fired by MailGun routing API
     * @return object
     */
    public function fetchMessageFromPayload (stdClass $payload) {
        return $this->getMessageFromPayload($payload)->get();
    }

}
