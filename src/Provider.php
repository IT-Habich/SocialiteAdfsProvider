<?php

namespace CodeAdminDe\SocialiteAdfsProvider;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class Provider extends AbstractProvider
{
    /**
     *  ADFS Endpoints.
     */
    protected $authorize_endpoint = '/adfs/oauth2/authorize';
    protected $token_endpoint = '/adfs/oauth2/token';
    protected $config_endpoint = '/adfs/.well-known/openid-configuration';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['openid'];

    /**
     * Get the ADFS server from services configuration.
     */
    protected function getAdfsServer()
    {
        return config('services.Adfs.adfs_server');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getAdfsServer().$this->authorize_endpoint, $state);
    }

    /**
     * Get the OpenID configuration URL of the provider.
     *
     * @return string
     */
    protected function getOpenIdConfigUrl()
    {
        return $this->getAdfsServer().$this->config_endpoint;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getAdfsServer().$this->token_endpoint;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        return (array) JWT::decode($token, JWK::parseKeySet($this->getKeySet()));
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => null,
            'avatar' => null,
            'nickname' => isset($user['user_name']) ? $user['user_name'] : null,
            'name' => isset($user['display_name']) ? $user['display_name'] : null,
            'email' => $user['email'],
        ]);
    }

    /**
     * Get the current JWT signing keys.
     *
     * @return array
     */
    private function getKeySet()
    {
        $oConfig = $this->getOpenIdConfiguration();

        try {
            $response = $this->getHttpClient()->get($oConfig->jwks_uri, array('http_errors' => true));
        } catch(\Exception $e) {
            throw new \Exception("JWT signing keys could not be fetched from IDP.");
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the current OpenID configuration from provider.
     *
     * @return object
     */
    private function getOpenIdConfiguration()
    {
        try {
            $response = $this->getHttpClient()->get($this->getOpenIdConfigUrl(), array('http_errors' => true));
        } catch(\Exception $e) {
            throw new \Exception("OpenID configuration could not be fetched from IDP.");
        }

        return json_decode($response->getBody());
    }
}
