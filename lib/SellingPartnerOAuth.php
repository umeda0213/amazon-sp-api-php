<?php

namespace ClouSale\AmazonSellingPartnerAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class SellingPartnerOAuth
{
    /**
     * @param $clientId
     * @param $clientSecret
     * @return mixed
     *
     * @throws GuzzleException
     */
    public static function getAccessTokenFromClientCredentials($clientId, $clientSecret)
    {
        $client = new Client();
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'sellingpartnerapi::migration'
        ];
        $options = array_merge([
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
            RequestOptions::HTTP_ERRORS => false,
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
        ], $params ? [RequestOptions::FORM_PARAMS => $params] : []);

        $response = $client->request('POST', 'https://api.amazon.com/auth/o2/token', $options);

        $body = $response->getBody()->getContents();
        $bodyAsJson = json_decode($body, true);

        return $bodyAsJson;
    }

    /**
     * @param $refreshToken
     * @param $clientId
     * @param $clientSecret
     *
     * @throws GuzzleException
     */
    public static function getAccessTokenFromRefreshToken($refreshToken, $clientId, $clientSecret)
    {
        $client = new Client();
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ];
        $options = array_merge([
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
            RequestOptions::HTTP_ERRORS => false,
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
        ], $params ? [RequestOptions::FORM_PARAMS => $params] : []);

        $response = $client->request('POST', 'https://api.amazon.com/auth/o2/token', $options);

        $body = $response->getBody()->getContents();
        $bodyAsJson = json_decode($body, true);

        return $bodyAsJson;
    }

    /**
     * @throws GuzzleException
     * @throws SellingPartnerOAuthException
     */
    public static function getRefreshTokenFromLwaAuthorizationCode(
        string $lwaAuthorizationCode,
        string $clientId,
        string $clientSecret,
        string $redirectUri
    ) {
        $client = new Client();
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $lwaAuthorizationCode,
            'redirect_uri' => $redirectUri,
        ];
        $options = array_merge([
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
            RequestOptions::HTTP_ERRORS => false,
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
        ], $params ? [RequestOptions::FORM_PARAMS => $params] : []);

        $response = $client->request('POST', 'https://api.amazon.com/auth/o2/token', $options);

        $body = $response->getBody()->getContents();
        $bodyAsJson = json_decode($body, true);
        if (isset($bodyAsJson['error_description'])) {
            throw new SellingPartnerOAuthException($bodyAsJson['error_description'] . ' / ' . $bodyAsJson['error']);
        }

        return $bodyAsJson;
    }
}
