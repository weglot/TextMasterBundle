<?php

namespace Weglot\TextMasterBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TextMasterApi
{
    private Client $client;

    public const BASE_TM_API_URL = 'https://api.';
    public const API_URI = 'textmaster.com/v1/';
    public const SANDBOX_API_URI = 'textmasterstaging.com/v1/';

    public const STAGING_ENV = 'staging';
    public const PROD_ENV = 'production';

    public const COMPLETED_PROJECT_STATUS = 'completed';

    public const ROUTES = [
        'getProject' => ['method' => 'GET', 'url' => 'clients/projects/{projectId}'],
        'getProjectQuotation' => ['method' => 'GET', 'url' => 'clients/projects/quotation'],
        'createProject' => ['method' => 'POST', 'url' => 'clients/projects'],
        'launchProject' => ['method' => 'PUT', 'url' => 'clients/projects/{projectId}/launch'],
        'updateProject' => ['method' => 'PUT', 'url' => 'clients/projects/{projectId}'],
        'addDocument' => ['method' => 'POST', 'url' => 'clients/projects/{projectId}/documents'],
        'completeDocument' => ['method' => 'PUT', 'url' => 'clients/projects/{projectId}/documents/{documentId}/complete'],
        'getDocument' => ['method' => 'GET', 'url' => 'clients/projects/{projectId}/documents/{documentId}'],
        'getAbilities' => ['method' => 'GET', 'url' => 'clients/abilities?activity=translation&page={page}'],
        'getAuthorsForProject' => ['method' => 'GET', 'url' => 'clients/projects/{projectId}/my_authors?status={status}'],
        'getCategories' => ['method' => 'GET', 'url' => 'public/categories'],
        'setOptions' => ['method' => 'PUT', 'url' => 'clients/projects/{projectId}/activate_tm_options'],
    ];

    public function __construct(string $apiKey, string $apiSecret, string $textmasterEnv)
    {
        $baseUri = self::BASE_TM_API_URL;
        $baseUri .= self::PROD_ENV === $textmasterEnv ? self::API_URI : self::SANDBOX_API_URI;

        $this->client = $this->createGuzzleClient([
            'key' => $apiKey,
            'secret' => $apiSecret,
            'base_uri' => $baseUri,
        ]);
    }

    /**
     * @param array<string, mixed> $project
     *
     * @throws GuzzleException
     */
    public function createProject(array $project): ResponseInterface
    {
        $routeParams = self::ROUTES['createProject'];

        return $this->request($routeParams['url'], $routeParams['method'], ['project' => $project]);
    }

    /**
     * @param array<string, mixed> $project
     *
     * @throws GuzzleException
     */
    public function updateProject(string $textMasterProjectId, array $project): ResponseInterface
    {
        $routeParams = self::ROUTES['updateProject'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method'], ['project' => $project]);
    }

    /**
     * @throws GuzzleException
     */
    public function launchProject(string $textMasterProjectId): ResponseInterface
    {
        $routeParams = self::ROUTES['launchProject'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @param array<string, mixed> $documents
     *
     * @throws GuzzleException
     */
    public function addDocumentsToProject(string $textMasterProjectId, array $documents): ResponseInterface
    {
        $routeParams = self::ROUTES['addDocument'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method'], ['document' => $documents]);
    }

    /**
     * @throws GuzzleException
     */
    public function completeDocument(string $documentId, string $textMasterProjectId): ResponseInterface
    {
        $routeParams = self::ROUTES['completeDocument'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId, '{documentId}' => $documentId]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @param array<string, mixed> $project
     *
     * @throws GuzzleException
     */
    public function setProjectOptions(string $textMasterProjectId, array $project): ResponseInterface
    {
        $routeParams = self::ROUTES['setOptions'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method'], ['project' => $project]);
    }

    /**
     * @throws GuzzleException
     */
    public function getProject(string $textMasterProjectId): ResponseInterface
    {
        $routeParams = self::ROUTES['getProject'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @throws GuzzleException
     */
    public function getDocument(string $textMasterProjectId, string $textMasterDocumentId): ResponseInterface
    {
        $routeParams = self::ROUTES['getDocument'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId, '{documentId}' => $textMasterDocumentId]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @param array<string, mixed> $project
     *
     * @throws GuzzleException
     */
    public function getProjectQuotation(array $project): ResponseInterface
    {
        $routeParams = self::ROUTES['getProjectQuotation'];

        return $this->request($routeParams['url'], $routeParams['method'], ['project' => $project]);
    }

    /**
     * @throws GuzzleException
     */
    public function getCategories(): ResponseInterface
    {
        $routeParams = self::ROUTES['getCategories'];

        return $this->request($routeParams['url'], $routeParams['method']);
    }

    /**
     * @throws GuzzleException
     */
    public function getAbilities(string $page): ResponseInterface
    {
        $routeParams = self::ROUTES['getAbilities'];
        $url = $this->formatUrl($routeParams['url'], ['{page}' => $page]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @throws GuzzleException
     */
    public function getAuthorsForProject(string $textMasterProjectId, string $status = 'my_textmaster'): ResponseInterface
    {
        $routeParams = self::ROUTES['getAuthorsForProject'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId, '{status}' => $status]);

        return $this->request($url, $routeParams['method']);
    }

    public function extractErrorFromResponse(ResponseInterface $response, string $format = 'html'): string
    {
        $errorMsg = '';
        $lineBreaker = 'html' === $format ? '</br>' : "\n";

        try {
            $decodedResponse = json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Exception) {
            return $errorMsg;
        }

        if (
            !\is_array($decodedResponse)
            || !isset($decodedResponse['errors'])
            || !is_iterable($decodedResponse['errors'])
        ) {
            return $errorMsg;
        }

        foreach ($decodedResponse['errors'] as $type => $messagesArray) {
            $errorMsg .= "Type of error: {$type} ".$lineBreaker;
            if (\is_array($messagesArray)) {
                foreach ($messagesArray as $msg) {
                    $errorMsg .= $msg.$lineBreaker;
                }
            }
        }

        return $errorMsg;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws GuzzleException
     */
    private function request(string $url, string $method, array $payload = []): ResponseInterface
    {
        $request = new Request($method, $url);

        return $this->client->send($request, [RequestOptions::JSON => $payload]);
    }

    /**
     * @param array{key: string, secret: string, base_uri: string} $options
     */
    private function createGuzzleClient(array $options): Client
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest(static function (RequestInterface $request) use ($options) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));

            return $request
                ->withHeader('Apikey', $options['key'])
                ->withHeader('Date', $date->format('Y-m-d H:i:s'))
                ->withHeader('Signature', sha1($options['secret'].$date->format('Y-m-d H:i:s')))
            ;
        }));

        unset($options['key'], $options['secret']);
        $options = array_merge($options, ['handler' => $stack]);

        return new Client($options);
    }

    /**
     * @param array<string, string> $parameters
     */
    private function formatUrl(string $url, array $parameters): string
    {
        foreach ($parameters as $placeholder => $value) {
            $url = str_replace($placeholder, $value, $url);
        }

        return $url;
    }
}
