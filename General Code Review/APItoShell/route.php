<?php

require_once('classes/db.php');
require_once('classes/utils.php');
require_once('classes/user.php');
require_once('classes/file.php');

/*
 * GET / HEAD → documentation
 * POST       → API actions
 */

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'HEAD') {

    require_once('views/header.php');
    require_once('views/documentation.php');
    require_once('views/footer.php');

} else {

    // Only JSON API
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {

        $data = (array) json_decode(file_get_contents('php://input'));

        if (isset($data['token'])) {
            $token = $data['token'];
        }
        if (isset($data['uuid'])) {
            $uuid = $data['uuid'];
        }
        if (isset($data['sig'])) {
            $sig = $data['sig'];
        }
        if (isset($data['username'])) {
            $username = $data['username'];
        }
        if (isset($data['password'])) {
            $password = $data['password'];
        }
        if (isset($data['filename'])) {
            $filename = $data['filename'];
        }
        if (isset($data['content'])) {
            $content = $data['content'];
        }

    } else {
        respond_with(["error" => "API only accepts json"]);
    }

    $user = NULL;

    if (isset($token)) {
        $user = User::detokenize($token);
    }

    // -------- ACTION ROUTER --------

    if ($_GET['action'] === "register") {

        if (isset($username) && isset($password)) {
            $user = User::register($username, $password);
            if ($user) {
                respond_with(["token" => User::tokenize($user)]);
            }
        }

        respond_with(["error" => "Invalid or missing credentials"]);

    } elseif ($_GET['action'] === "login") {

        if (isset($username) && isset($password)) {
            $user = User::login($username, $password);
            if ($user) {
                respond_with(["token" => User::tokenize($user)]);
            }
        }

        respond_with(["error" => "Invalid or missing credentials"]);

    } elseif ($_GET['action'] === "files") {

        if ($user !== NULL) {
            respond_with(["files" => $user->all_files()]);
        }

        respond_with(["error" => "Authorisation fail"]);

    } elseif ($_GET['action'] === "file") {

        if ($user !== NULL) {
            $file = $user->file($uuid, $sig);
            respond_with(["file" => $file]);
        }

        respond_with(["error" => "Authorisation fail"]);

    } elseif ($_GET['action'] === "upload") {

        if ($user !== NULL) {
            File::upload($filename, $content, $user->id);
        }

        respond_with(["error" => "Authorisation fail"]);

    } else {

        respond_with(["error" => "Invalid action"]);

    }
}
?>
