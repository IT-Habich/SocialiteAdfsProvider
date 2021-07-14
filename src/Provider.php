<?php

namespace CodeAdminDe\SocialiteAdfsProvider;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class Provider extends AbstractProvider
{
    /**
     *  ADFS Endpoints.
     */
    protected $authorize_endpoint = '/adfs/oauth2/authorize';
    protected $token_endpoint = '/adfs/oauth2/token';

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
        return json_decode(base64_decode(explode('.', $token)[1]), true);
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
}
