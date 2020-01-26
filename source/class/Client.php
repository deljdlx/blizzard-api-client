<?php

namespace ElBiniou\BlizzardApi;

class Client
{

    private $id;
    private $key;


    private $redirectURL;


    private $authURL = 'https://eu.battle.net/oauth/authorize';
    private $accessTokenURL = 'https://eu.battle.net/oauth/token';
    private $resourceOwnerDetailsURL = 'https://eu.battle.net/oauth/userinfo';

    private $serviceRootURL = 'https://eu.api.blizzard.com';


    /**
     * @var Provider
     */
    private $provider;


    public function __construct($clientId, $clientKey, $redirectURL)
    {
        $this->id = $clientId;
        $this->key = $clientKey;

        $this->redirectURL = $redirectURL;

        $this->provider = new Provider($this);
    }

    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    public function getAuthURL()
    {
        return $this->authURL;
    }

    public function getAccessTokenURL()
    {
        return $this->accessTokenURL;
    }

    public function getResourceOwnerDetailsURL()
    {
        return $this->resourceOwnerDetailsURL;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function listen()
    {
        return $this->provider->listen();
    }


    public function getProvider()
    {
        return $this->provider;
    }


    public function query($endPoint, $method = 'GET', $data = [])
    {

        $parameters = '';
        foreach ($data as $name => $value) {
            $parameters .= '&' . $name . '=' . urlencode($value);
        }

        $queryStart = '?';
        if(strpos($endPoint, '?')) {
            $queryStart = '';
        }

        $url = $this->serviceRootURL . $endPoint . $queryStart . $parameters . ' &access_token=' . $this->provider->getToken();


        return $this->provider->query($url, $method);

    }


}






