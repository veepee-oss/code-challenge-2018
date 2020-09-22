<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Service\LoggerService\LoggerServiceInterface;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use AppBundle\Domain\Service\MovePlayer\AskPlayerNameInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use Davamigo\HttpClient\Domain\HttpClient;
use Davamigo\HttpClient\Domain\HttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AskPlayerApiService
 *
 * @package AppBundle\Service\MovePlayer
 */
class AskPlayerApiService implements AskNextMovementInterface, AskPlayerNameInterface
{
    /** @var HttpClient */
    protected $httpClient;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var LoggerServiceInterface */
    protected $dbLogger;

    /** @var LoggerInterface */
    private $logger;

    /** @var int */
    protected $timeout;

    /** @var string endpoint contants */
    private const ENDPOINT_ASK_NAME = '/name';
    private const ENDPOINT_ASK_MOVE = '/move';

    /**
     * ApiPlayerService constructor.
     *
     * @param HttpClient             $httpClient
     * @param ValidatorInterface     $validator
     * @param LoggerServiceInterface $dbLogger
     * @param LoggerInterface        $logger
     * @param int                    $timeout
     */
    public function __construct(
        HttpClient $httpClient,
        ValidatorInterface $validator,
        LoggerServiceInterface $dbLogger,
        LoggerInterface $logger,
        $timeout = 3
    ) {
        $this->httpClient = $httpClient;
        $this->validator = $validator;
        $this->dbLogger = $dbLogger;
        $this->logger = $logger;
        $this->timeout = $timeout;
    }

    /**
     * Asks for the name of the player
     *
     * @param string $url    the base URL to call
     * @param string $player the player UUID
     * @param string $game   the game UUID (optional)
     * @return array['name', 'email'] The player name and email
     * @throws MovePlayerException
     */
    public function askPlayerName(string $url, string $player, string $game = null) : array
    {
        $endpointURL = $url . self::ENDPOINT_ASK_NAME;

        // Call to the REST API
        $responseData = $this->callToApi($endpointURL, $player, $game, null);

        // Extract the data from the response
        $name = $responseData['name'] ?? null;
        $email = $responseData['email'] ?? null;

        // Constraints definition
        $notBlankConstraint = new Assert\NotBlank();
        $emailConstraint = new Assert\Email();

        // Use the validator to validate the name
        $errorList = $this->validator->validate($name, $notBlankConstraint);
        if (0 !== count($errorList)) {
            throw new MovePlayerException($this->buildErrorMessage(
                $endpointURL,
                'Name is required.',
                $player,
                $game
            ));
        }

        // Use the validator to validate the email
        $errorList = $this->validator->validate($email, array($notBlankConstraint, $emailConstraint));
        if (0 !== count($errorList)) {
            throw new MovePlayerException($this->buildErrorMessage(
                $endpointURL,
                'Valid email is required.',
                $player,
                $game
            ));
        }

        return array(
            'name' => $name,
            'email' => $email
        );
    }

    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param string $url     the base URL to call
     * @param string $player  the player UUID
     * @param string $game    the game UUID
     * @param string $request the player request
     * @return string The next movement
     * @throws MovePlayerException
     */
    public function askNextMovement(string $url, string $player, string $game, string $request) : string
    {
        $endpointURL = $url . self::ENDPOINT_ASK_MOVE;

        // Call to the REST API
        $responseData = $this->callToApi($endpointURL, $player, $game, $request);

        // Extract the data from the response
        $move = $responseData['move'] ?? null;

        // Constraints definition
        $notBlankConstraint = new Assert\NotBlank();

        // Use the validator to validate the name
        $errorList = $this->validator->validate($move, $notBlankConstraint);
        if (0 !== count($errorList)) {
            throw new MovePlayerException($this->buildErrorMessage(
                $endpointURL,
                'Move is required.',
                $player,
                $game
            ));
        }

        return $move;
    }

    /**
     * Calls to the API
     *
     * @param string $requestUrl the URL of the endpoint to call
     * @param string $player the player UUID
     * @param string $game the game UUID
     * @param string $requestBody the player request
     * @return array The read data
     * @throws MovePlayerException
     */
    private function callToApi(
        string $requestUrl,
        string $player,
        string $game = null,
        string $requestBody = null
    ) : array {
        $game = $game ?? 'new-game';

        $requestHeaders = array(
            'Content-Type' => 'application/json; charset=UTF-8'
        );

        $options = array(
            CURLOPT_CONNECTTIMEOUT  => $this->timeout,
            CURLOPT_TIMEOUT         => $this->timeout
        );

        $this->logger->debug(
            'AskPlayerApiService - Calling player API for ' .
            'game "' . $game . '" and player "' . $player . '".'
        );

        $this->logger->debug(
            'AskPlayerApiService - Request created - ' .
            'URL: ' . $requestUrl . ' - ' .
            'Body: ' . $requestBody
        );

        try {
            $response = $this->httpClient->post($requestUrl, $requestHeaders, $requestBody, $options)->send();
            $responseCode = $response->getStatusCode();
        } catch (HttpException $exc) {
            $this->logger->error(
                'AskPlayerApiService - HttpException occurred - Message: "' . $exc->getMessage() . '".'
            );

            $this->dbLogger->log($game, $player, $this->buildErrorContextArray(
                $requestUrl,
                $requestHeaders,
                $requestBody,
                $exc->getMessage()
            ));

            throw new MovePlayerException(
                $this->buildErrorMessage($requestUrl, $exc->getMessage(), $player, $game),
                0,
                $exc
            );
        }

        $responseBody = $response->getBody(true);

        $responseData = json_decode($responseBody, true);
        if (null === $responseData || !is_array($responseData)) {
            $message = 'Invalid API response!';
            if (JSON_ERROR_NONE != json_last_error()) {
                $message .= ' - ' . json_last_error_msg();
            }
            $message .= ' - URL: ' . $requestUrl . ' - Body: ' . $responseBody;

            $this->logger->error(
                'AskPlayerApiService - Invalid response received - ' . $responseBody
            );

            $this->logger->error(
                'AskPlayerApiService - Error decoding JSON message - ' . $message
            );

            $this->dbLogger->log($game, $player, $this->buildErrorContextArray(
                $requestUrl,
                $requestHeaders,
                $requestBody,
                $message,
                $responseCode,
                $response->getHeaderLines(),
                $responseBody
            ));

            throw new MovePlayerException($message);
        }

        $this->logger->debug(
            'AskPlayerApiService - Valid response received - ' .
            'Code: ' . $responseCode . ' - ' .
            'Body: ' . $responseBody
        );

        $this->dbLogger->log($game, $player, $this->buildErrorContextArray(
            $requestUrl,
            $requestHeaders,
            $requestBody,
            null,
            $responseCode,
            $response->getHeaderLines(),
            $responseBody
        ));

        return $responseData;
    }

    /**
     * Fotrmats an error message
     *
     * @param string $endpointURL
     * @param string $message
     * @param string $player
     * @param string $game
     * @return string
     */
    private function buildErrorMessage(
        string $endpointURL,
        string $message,
        string $player,
        string $game = null
    ) : string {
        $msg = 'An error occurred calling a player API! ';
        $msg .= PHP_EOL . 'URL: ' . $endpointURL;
        $msg .= PHP_EOL . 'Message: ' . $message;
        $msg .= PHP_EOL . 'Player ' . $player;

        if (null !== $game) {
            $msg .= PHP_EOL . 'Game ' . $game;
        }

        return $msg;
    }

    /**
     * @param string $requestUrl
     * @param array $requestHeaders
     * @param string $requestBody
     * @param string|null $errorMessage
     * @param int|null $responseCode
     * @param array|null $responseHeaders
     * @param string|null $responseBody
     * @return array
     */
    private function buildErrorContextArray(
        string $requestUrl,
        array  $requestHeaders,
        string  $requestBody = null,
        string $errorMessage = null,
        int    $responseCode = null,
        array  $responseHeaders = null,
        string $responseBody = null
    ) : array {
        $result = [
            'requestUrl' => $requestUrl,
            'requestHeaders' => $requestHeaders,
            'requestBody' => $requestBody
        ];

        if (null !== $responseCode) {
            $result['responseCode'] = $responseCode;
        }

        if (null !== $responseHeaders) {
            $result['responseHeaders'] = $responseHeaders;
        }

        if (null !== $responseBody) {
            $result['responseBody'] = $responseBody;
        }

        if (null !== $errorMessage) {
            $result['errorMessage'] = $errorMessage;
        }

        return $result;
    }
}
