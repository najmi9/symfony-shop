<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Get The User Data From Google Api.
 */
class GoogleService
{
    private HttpClientInterface $httpClient;
    private UrlGeneratorInterface $urlGenerator;
    private string $googleId;
    private string $googleSecret;

    public function __construct(
        string $google_secret,
        string $google_id,
        HttpClientInterface $httpClient,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->googleSecret = $google_secret; // in'.env file' secret key get it from google
        $this->googleId = $google_id; // in'.env file' public key get it from google
        $this->httpClient = $httpClient; // to make call request to verify the captcha
        $this->urlGenerator = $urlGenerator; // to generate a route according to its name
    }

    /**
     * this method will call the googl API with post request to get the user infos.
     *
     * @param string[] $credantials
     *
     * @return string[]|null
     */
    public function loadData(array $credantials): ?array
    {
        $code = $credantials['code']; //google redirect the user with code as a parameter in the url
        $state = $credantials['state']; // the same
        if ('google' === $state) {
            $redirectUri = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

            $url = sprintf('https://oauth2.googleapis.com/token?client_id=%s&client_secret=%s&code=%s&grant_type=authorization_code&redirect_uri=%s', $this->googleId, $this->googleSecret, $code, $redirectUri);

            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Content-Length' => 0,
                    'Accept' => 'application/json',
                ],
            ]);
            // extract data from a token coded in base64
            $info = $response->toArray()['id_token'];
            $jwt = explode('.', $info);

            return json_decode(base64_decode($jwt[1], true), true);
        }

        return null;
    }
}
