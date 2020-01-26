<?php

namespace ElBiniou\BlizzardApi;

class Provider
{

    private $client;
    private $redirectURL;

    /**
     * @var []
     */
    private $storage;


    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->redirectURL = $this->client->getRedirectURL();

        $this->driver = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $this->client->getId(),    // The client ID assigned to you by the provider
            'clientSecret' => $this->client->getKey(),   // The client password assigned to you by the provider
            'redirectUri' => $this->client->getRedirectURL(),
            'urlAuthorize' => $this->client->getAuthURL(),
            'urlAccessToken' => $this->client->getAccessTokenURL(),
            'urlResourceOwnerDetails' => $this->client->getResourceOwnerDetailsURL()
        ]);

        $this->storage = &$_SESSION;
    }

    public function getAuthURL()
    {
        $authorizationUrl = $this->driver->getAuthorizationUrl();
        return $authorizationUrl;
    }

    public function listen($scope = null)
    {
        if($scope === null) {
            $scope = $_GET;
        }
        if (!isset($scope['code'])) {
            $this->gotoAuthURL();
        }
        else {
            if(!$this->checkState($scope)) {
                $this->fail();
            }
            else {

                if(!$this->get('token')) {
                    $accessToken = $this->driver->getAccessToken('authorization_code', [
                        'code' => $scope['code']
                    ]);

                    $this->store('token', $accessToken);
                }

                $token = $this->getToken();
                if($token) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getToken()
    {
        return $this->get('token');
    }


    public function checkState($scope)
    {
        if(
            empty($scope['state']) ||
            ($this->get('oauth2state') && $scope['state'] !== $this->get('oauth2state'))
        ) {
            $this->delete('oauth2state');
            return false;
        }
        return true;

    }

    public function fail()
    {
        exit('wrong state');
    }



    public function gotoAuthURL()
    {
        $authorizationUrl = $this->getAuthURL();

        $this->store(
            'oauth2state',
            $this->driver->getState()
        );
        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
        exit();
    }


    public function store($key, $value)
    {
        $this->storage[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        if(array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }
        return null;
    }

    public function delete($key)
    {
        if(array_key_exists($key, $this->storage)) {
            unset($this->storage[$key]);
        }
        return $this;
    }


    public function query($url, $method = 'GET')
    {
        $request = $this->driver->getAuthenticatedRequest(
            'GET',
            $url,
            $this->getToken()
        );

        return $this->driver->getParsedResponse($request);
    }




}






