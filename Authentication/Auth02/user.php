<?php
use Namshi\JOSE\JWS;

class User {

    public static function logout() {
        setcookie("auth", NULL, time() - 10);
    }

    public static function createcookie($user) {
        return md5($user);
    }

    public static function getuserfromcookie($auth) {
        // VULNERABLE FUNCTION - Looks up user by MD5 hash of username
        $sql = "SELECT * FROM users where userhash=\"";
        $sql.= mysql_real_escape_string($auth);
        $sql.= "\"";
        $result = mysql_query($sql);

        if ($result) {
            $row = mysql_fetch_assoc($result);
            return $row['login'];
        }
        return NULL;
    }

    public static function login($user, $password) {
        // Authenticates user via login and password
        $sql = "SELECT * FROM users where login=\"";
        $sql.= mysql_real_escape_string($user);
        $sql.= "\" and password=md5(\"myseedgoeshere\"";
        $sql.= mysql_real_escape_string($password);
        $sql.= "\")\"";
        $result = mysql_query($sql);

        if ($result) {
            $row = mysql_fetch_assoc($result);
            if ($user === $row['login']) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function register($user, $password) {
        // Registers a new user
        $sql = "INSERT INTO users (login, password, userhash) values (\"";
        $sql.= mysql_real_escape_string($user);
        $sql.= "\", md5(\"myseedgoeshere";
        $sql.= mysql_real_escape_string($password);
        $sql.= "\"), md5(\"";
        $sql.= mysql_real_escape_string($user);
        $sql.= "\"))";
        $result = mysql_query($sql);
        
        if ($result) {
            return TRUE;
        } else {
            echo mysql_error();
            return FALSE;
        }
    }
}