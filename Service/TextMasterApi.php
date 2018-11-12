<?php
/**
 * Created by PhpStorm.
 * User: etienne
 * Date: 29/10/2018
 * Time: 12:04.
 */

namespace Weglot\TextMasterBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;

class TextMasterApi
{
    /** Client Client */
    private $client;
    private $basicHeaders = [];

    const BASE_TM_API_URL = 'http://api.';
    const API_URI = 'textmaster.com/v1/';
    const SANDBOX_API_URI = 'textmastersandbox.com/v1/';

    const STAGING_ENV = 'staging';
    const PROD_ENV = 'production';

    const COMPLETED_PROJECT_STATUS = 'completed';

    const ROUTES = [
        'getProject' => ['method' => 'GET', 'url' => 'clients/projects/{projectId}'],
        'createProject' => ['method' => 'POST', 'url' => 'clients/projects'],
        'launchProject' => ['method' => 'PUT', 'url' => 'clients/projects/{projectId}/launch'],
        'addDocuments' => ['method' => 'POST', 'url' => 'clients/projects/{projectId}/batch/documents'],
        'completeDocument' => ['method' => 'PUT', 'url' => 'clients/projects/{projectId}/documents/{documentId}/complete'],
    ];

    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->basicHeaders['key'] = $apiKey;
        $this->basicHeaders['secret'] = $apiSecret;
    }

    public function initClient(string $env)
    {   
        $baseUri = self::BASE_TM_API_URL;
        $baseUri .= $env === self::PROD_ENV ? self::API_URI : self::SANDBOX_API_URI;

        $this->basicHeaders['base_uri'] = self::BASE_TM_API_URL.self::API_URI;
        $this->client = $this->createGuzzleClient($this->basicHeaders);    
    }

    /**
     * @param array $project
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createProject(array $project): Response
    {
        $routeParams = self::ROUTES['createProject'];

        return $this->request($routeParams['url'], $routeParams['method'], ['project' => $project]);
    }

    /**
     * @param string $textMasterProjectId
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProject(string $textMasterProjectId): Response
    {
        $routeParams = self::ROUTES['getProject'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @param string $textMasterProjectId
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function launchProject(string $textMasterProjectId): Response
    {
        $routeParams = self::ROUTES['launchProject'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method']);
    }

    /**
     * @param string $textMasterProjectId
     * @param array  $documents
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addDocumentsToProject(string $textMasterProjectId, array $documents): Response
    {
        $routeParams = self::ROUTES['addDocuments'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId]);

        return $this->request($url, $routeParams['method'], ['documents' => $documents]);
    }

    /**
     * @param string $documentId
     * @param string $textMasterProjectId
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function completeDocument(string $documentId, string $textMasterProjectId): Response
    {
        $routeParams = self::ROUTES['completeDocument'];
        $url = $this->formatUrl($routeParams['url'], ['{projectId}' => $textMasterProjectId, '{documentId}' => $documentId]);

        return $this->request($url, $routeParams['method']);
    }

    public function extractErrorFromResponse(Response $response, string $format = 'html'): string
    {
        $errorMsg = '';
        $lineBreaker = 'html' === $format ? '</br>' : "\n";

        $decodedResponse = json_decode($response->getBody()->getContents(), true);
        foreach ($decodedResponse['errors'] as $type => $messagesArray) {
            $errorMsg .= "Type of error: $type".$lineBreaker;
            if (is_array($messagesArray)) {
                foreach ($messagesArray as $msg) {
                    $errorMsg .= $msg.$lineBreaker;
                }
            }
        }

        return $errorMsg;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $payload
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $url, string $method, array $payload = []): Response
    {
        $request = new Request($method, $url);
        $response = $this->client->send($request, [RequestOptions::JSON => $payload]);

        return $response;
    }

    /**
     * @param array $options
     *
     * @return Client
     */
    private function createGuzzleClient(array $options): Client
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($options) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));

            return $request
                    ->withHeader('Apikey', $options['key'])
                    ->withHeader('Date', $date->format('Y-m-d H:i:s'))
                    ->withHeader('Signature', sha1($options['secret'].$date->format('Y-m-d H:i:s')))
            ;
        }));
        unset($options['key']);
        unset($options['secret']);
        $options = array_merge($options, ['handler' => $stack]);

        return new Client($options);
    }

    private function formatUrl(string $url, array $parameters): string
    {
        foreach ($parameters as $placeholder => $value) {
            $url = str_replace($placeholder, $value, $url);
        }

        return $url;
    }
}
