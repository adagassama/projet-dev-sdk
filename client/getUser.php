<?php

class GetUser
{

    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;
    protected $uri_access_token;
    protected $user_endpoint;


    public function GetUserAuth($token)
    {
        $context = stream_context_create([
            'http' => [
                "header" => [
                    "Authorization: Bearer " . $token
                ]
            ]
        ]);

        $result = file_get_contents($this->user_endpoint, false, $context);
        return json_decode($result, true);
    }



    public function GetTokenAuth($params)
    {
        $params = array_merge([
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "redirect_uri" => $this->redirect_uri,
        ], $params);
        $result = file_get_contents($this->uri_access_token . "?" . http_build_query($params));
        return json_decode($result, true)["access_token"];
    }

    public function handleSuccess($client_id, $client_secret, $redirect_uri, $uri_access_token, $user_endpoint)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->uri_access_token = $uri_access_token;
        $this->user_endpoint = $user_endpoint;


        ["code" => $code, "state" => $state] = $_GET;
        if ($state !== STATE) {
            throw new \Exception("Erreur d'authentification");
        }
        $token = $this->GetTokenAuth([
            "grant_type" => "authorization_code",
            "code" => $code,
        ],);
        $user = $this->GetUserAuth($token);
        echo json_encode($user);
    }
}
