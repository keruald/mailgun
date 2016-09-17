<?php

namespace Keruald\Mailgun;

use GuzzleHttp\ClientInterface;

use InvalidArgumentException;
use stdClass;

class MailgunMessage {

    ///
    /// Private properties
    ///

    /**
     * @var stdClass
     */
    private $message = null;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $key;

    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of the MailgunMessage object.
     *
     * @param \GuzzleHttp\Client $client HTTP client
     * @param string $url The URL of the message to fetch.
     * @param string $key The API key to use to fetch the message.
     */
    public function __construct (ClientInterface $client, $url, $key) {
        $this->client = $client;
        $this->url = $url;
        $this->key = $key;
    }

    /**
     * Initializes a new instance of the MailgunMessage object from a payload.
     *
     * @param \GuzzleHttp\Client $client HTTP client
     * @param stdClass $payload The payload fired by MailGun routing API
     * @param string $key The API key to use to fetch the message.
     */
    public static function loadFromEventPayload (ClientInterface $client, stdClass $payload, $key) {
        $url = self::extractUrlFromEventPayload($payload);
        return new self($client, $url, $key);
    }

    ///
    /// Public API methods
    ///

    /**
     * Gets a JSON representation of a mail through Mailgun API.
     *
     * @return stdClass
     */
    public function get () {
        if ($this->message === null) {
            $this->fetch();
        }

        return $this->message;
    }

    ///
    /// Helper methods to fetch a message from Mailgun.
    ///

    /**
     * Fetches the message through Mailgun API.
     *
     * If successful, fills the message property.
     *
     * @throws \RuntimeException when HTTP status code isn't 200
     */
    private function fetch () {
        $response = $this->client->request(
            'GET',
            $this->url,
            $this->getHttpOptions()
        );

        $result = $response->getBody();
        $this->message = json_decode($result);
    }

    /**
     * @return array
     */
    private function getHttpOptions () {
        return [
            'auth' => [
                'api',
                $this->key
            ]
        ];
    }

    ///
    /// Helper methods to process payload
    ///

    /**
     * Extracts the MailGun URL to retrieve a stored message.
     *
     * @param stdClass $payload The payload fired by MailGun routing API
     * @return string
     * @throw \InvalidArgumentException if payload doesn't contain URL where expected.
     */
    private static function extractUrlFromEventPayload (stdClass $payload) {
        if (!isset($payload->storage->url)) {
            throw new InvalidArgumentException("The payload should be an object with a storage.url property.");
        }
        return $payload->storage->url;
    }

}
