<?php

namespace App\Services;

/**
 * DEMO SKELETON: RestApiWrapper Interface
 * 
 * This service was originally responsible for:
 * - Making HTTP GET/POST requests to external APIs
 * - Handling authentication tokens
 * - Processing multipart form data
 * 
 * For demo purposes, all business logic has been removed.
 * In production, this would wrap HTTP client functionality.
 */
interface RestApiWrapperInterface
{
    /**
     * Make GET request
     * Original: Used GuzzleHttp Client to make GET requests
     * 
     * @param string $url
     * @return array
     */
    public function get($url);

    /**
     * Make POST request
     * Original: Used GuzzleHttp Client to make POST requests with JSON data
     * 
     * @param string $url
     * @param array $data
     * @return array
     */
    public function post($url, $data);

    /**
     * Make authenticated POST request
     * Original: Used GuzzleHttp Client to make POST requests with multipart data and auth token
     * 
     * @param string $url
     * @param array $data
     * @param string $authToken
     * @return array
     */
    public function postAuth($url, $data, $authToken);
}
