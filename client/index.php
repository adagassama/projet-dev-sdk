<?php

define("STATE", "fdsfvedfvedz");

require ("./client/getUser.php");

function getUser($token)
{
    $context = stream_context_create([
        'http'=>[
            "header" => [
                "Authorization: Bearer " . $token
            ]
        ]
    ]);

    $result = file_get_contents("http://server:8080/me", false, $context);
    return json_decode($result, true);
}

function getToken($params)
{
    $params = array_merge([
        "client_id" => "client_619b7efca4904",
        "client_secret" => "5a59658261c5495a7a05aa15e15efbea",
        "redirect_uri" => "https://localhost:8080/redirect_success",
    ], $params);

    $result = file_get_contents("http://server:8080/token?".http_build_query($params));
    return json_decode($result, true)["access_token"];
}
function getFacebookToken($params)
{
    $params = array_merge([
        "client_id" => "230203002417566",
        "client_secret" => "b650e11c6909ea6a7121ea2201a7c5b3",
        "redirect_uri" => "https://localhost/redirect_facebook",
    ], $params);
    $result = file_get_contents("https://graph.facebook.com/v2.10/oauth/access_token"."?".http_build_query($params));
    return json_decode($result, true)["access_token"];
}

 function getGoogleToken($params)
 {
    $params = array_merge([
        "client_id" => "260549229334-afm2rl1bn9lfcbktk3ekm99r89kj8tqg.apps.googleusercontent.com",
        "client_secret" => "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",
        "redirect_uri" => "https://localhost/redirect_google",
    ], $params);
    $result = file_get_contents("https://oauth2.googleapis.com/token"."?".http_build_query($params));
    return json_decode($result, true)["access_token"];
}

function getGithubToken($params)
{
    $params = array_merge([
        "client_id" => "14fbe1077e4ebd627fd8",
        "client_secret" => "9f4a58eaa926e304eac4b0b757118910dec255ed",
        "redirect_uri" => "https://localhost:443/redirect_github",
    ], $params);
    $result = file_get_contents("https://github.com/login/oauth/access_token" . "?" . http_build_query($params));
    return json_decode($result, true)["access_token"];
}


function getFacebookUser($token)
{
    $context = stream_context_create([
        'http'=>[
            "header" => [
                "Authorization: Bearer " . $token
            ]
        ]
    ]);

    $result = file_get_contents("https://graph.facebook.com/v2.10/me", false, $context);
    return json_decode($result, true);
}


function getGoogleUser($token)
{
    $context = stream_context_create([
        'http'=>[
            "header" => [
                "Authorization: Bearer " . $token
            ]
        ]
    ]);

    $result = file_get_contents("https://www.googleapis.com/oauth2/v2", false, $context);
    return json_decode($result, true);
}

function getGithubUser($token)
{
    $context = stream_context_create([
        'http'=>[
            "header" => [
                "Authorization: Bearer " . $token
            ]
        ]
    ]);

    $result = file_get_contents("https://api.github.com/user", false, $context);
    return json_decode($result, true);
}
//https://github.com/login/oauth/access_token

function getAuthUrl($baseUrl, $params)
{
    $params = array_merge([
        "state" => STATE,
        "response_type" => "code",
    ], $params);

    return $baseUrl."?".http_build_query($params);
}

function login()
{
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $url = getAuthUrl("http://localhost:8080/auth", [
            "redirect_uri" => "https://localhost/redirect_success",
            "client_id" => "client_619b7efca4904",
            "scope" => "basic",
        ]);
        echo "<a href='{$url}'>Se connecter via ServOauth</a> </br>";

        $urlFacebook = getAuthUrl("https://www.facebook.com/v2.10/dialog/oauth", [
            "redirect_uri" => "https://localhost/redirect_facebook",
            "client_id" => "230203002417566",
            "scope" => "public_profile,email",
        ]);
        echo "<a href='{$urlFacebook}'>Se connecter via Facebook</a></br>";

        $urlGoogle = getAuthUrl("https://accounts.google.com/o/oauth2/v2/auth", [
            "redirect_uri" => "https://localhost/redirect_google",
            "client_id" => "260549229334-afm2rl1bn9lfcbktk3ekm99r89kj8tqg.apps.googleusercontent.com",
            "scope" => "email",
        ]);
        echo "<a href='{$urlGoogle}'>Se connecter via Google</a></br>";

        $urlGithub = getAuthUrl("https://github.com/login/oauth/authorize", [
            "redirect_uri" => "https://localhost/redirect_github",
            "client_id" => "14fbe1077e4ebd627fd8",
            "scope" => "user",
        ]);
        echo "<a href='{$urlGithub}'>Se connecter via Github</a></br></br>";

        echo "<form method='POST'>";
        echo "<input name='username'/>";
        echo "<input name='password'/></br></br>";
        echo "<input type='submit' value='Valider'/>";
        echo "</form>";
    } else {
        $token = getToken(array_merge(["grant_type"=> "password"], $_POST));
        $user = getUser($token);
        echo json_encode($user);
    }
}

function handleSuccess()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== STATE) {
        throw new \Exception("Erreur d'authentification");
    }
    $token = getToken([
        "grant_type" => "authorization_code",
        "code" => $code,
    ]);
    $user = getUser($token);

    echo json_encode($user);
}


function handleFacebook()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== STATE) {
        throw new \Exception("Erreur d'authentification");
    }
    $token = getFacebookToken([
        "grant_type" => "authorization_code",
        "code" => $code,
    ]);
    $user = getFacebookUser($token);
    echo json_encode($user);
}

function handleGoogle()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== STATE) {
        throw new \Exception("Erreur d'authentification");
    }
    $token = getGoogleToken([
        "grant_type" => "authorization_code",
        "code" => $code,
    ]);
    $user = getGoogleUser($token);
    echo json_encode($user);
}

function handleGithub()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== STATE) {
        throw new \Exception("Erreur d'authentification");
    }
    $token = getGithubToken([
        "grant_type" => "authorization_code",
        "code" => $code,
    ]);
    $user = getGithubUser($token);
    echo json_encode($user);
}


$route = strtok($_SERVER["REQUEST_URI"], "?");

try {
    switch ($route) {
        case "/login":
            login();
            break;
        case '/redirect_success':
            
            handleSuccess(
                $client_id
            );
            break;
        case '/redirect_facebook':
            handleFacebook();
            break;
         case '/redirect_google':
             handleGoogle();
             break;
         case '/redirect_github':
            handleGoogle();
            break;
        default:
            throw new \RuntimeException();
        break;
    }
} catch (\RuntimeException $e) {
    http_response_code(404);
    echo "Not found";
} catch (\Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}
?>