<?php

define("STATE", "fdsfvedfvedz");

require("./getUser.php");

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
        $userHandle = new GetUser();
        // $token = getToken(array_merge(["grant_type"=> "password"], $_POST));
        $token = $userHandle->GetTokenAuth(array_merge(["grant_type"=> "password"], $_POST));
        // $user = getUser($token);
        $user = $userHandle->GetUserAuth($token);
        echo json_encode($user);
    }
}


$route = strtok($_SERVER["REQUEST_URI"], "?");

try {
    switch ($route) {
        case "/login":
            login();
            break;
        case '/redirect_success':
            $handleUser = new GetUser();
            $handleUser->handleSuccess(
                "client_619b7efca4904",
                "5a59658261c5495a7a05aa15e15efbea",
                "https://localhost:8080/redirect_success",
                "http://server:8080/token?",
                "http://server:8080/me"
            );
            break;
        case '/redirect_facebook':
            $handleUser = new GetUser();
            $handleUser->handleSuccess(
                "230203002417566",
                "b650e11c6909ea6a7121ea2201a7c5b3",
                "https://localhost/redirect_facebook",
                "https://github.com/login/oauth/access_token",
                "https://graph.facebook.com/v2.10/me"
            );
            break;
         case '/redirect_google':
            $handleUser = new GetUser();
            $handleUser->handleSuccess(
                "260549229334-afm2rl1bn9lfcbktk3ekm99r89kj8tqg.apps.googleusercontent.com",
                "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",
                "https://localhost/redirect_google",
                "https://oauth2.googleapis.com/token",
                "https://www.googleapis.com/oauth2/v2"
            );
             break;
         case '/redirect_github':
            $handleUser = new GetUser();
            $handleUser->handleSuccess(
                "14fbe1077e4ebd627fd8",
                "9f4a58eaa926e304eac4b0b757118910dec255ed",
                "https://localhost:443/redirect_github",
                "https://github.com/login/oauth/access_token",
                "https://api.github.com/user"
            );
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