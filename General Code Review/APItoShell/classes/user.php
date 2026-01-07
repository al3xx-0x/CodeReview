<?php

class User
{
    public $id;
    public $login;

    function __construct($id, $username)
    {
        $this->id = $id;
        $this->login = $username;
    }

    public static function register($username, $password)
    {
        $sql  = "SELECT * FROM users WHERE login=\"";
        $sql .= mysql_real_escape_string($username);
        $sql .= "\"";

        $result = mysql_query($sql);

        if ($result) {
            $row = mysql_fetch_assoc($result);

            if ($username === $row['login']) {
                return NULL; // User exists
            }

            $sql  = "INSERT INTO users (login, password) VALUES ('";
            $sql .= mysql_real_escape_string($username);
            $sql .= "', md5('";
            $sql .= mysql_real_escape_string($password);
            $sql .= "'))";

            if (mysql_query($sql)) {
                return new User(mysql_insert_id(), $username);
            }
        }

        return NULL;
    }

    public static function login($username, $password)
    {
        $sql  = "SELECT * FROM users WHERE login=\"";
        $sql .= mysql_real_escape_string($username);
        $sql .= "\" AND password=md5(\"";
        $sql .= mysql_real_escape_string($password);
        $sql .= "\")";

        $result = mysql_query($sql);

        if ($result) {
            $row = mysql_fetch_assoc($result);
            if ($username === $row['login']) {
                return new User($row['id'], $row['login']);
            }
        }

        return NULL;
    }

    public static function tokenize($user)
    {
        $token  = urlencode(base64_encode(serialize($user)));
        $token .= "--" . sign($token);
        return $token;
    }

    public static function detokenize($token)
    {
        list($userdata, $usersig) = explode("--", $token, 2);

        if ($usersig !== sign($userdata)) {
            respond_with(["error" => "Invalid authentication token"]);
        }

        return unserialize(base64_decode(urldecode($userdata)));
    }

    public function all_files()
    {
        return File::index($this->id);
    }

    public function file($uuid, $sig)
    {
        return File::get_file($this->id, $uuid, $sig);
    }
}
?>
