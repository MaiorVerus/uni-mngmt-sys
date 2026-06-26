
<?php

class AuthController
{
    public static function signup()
    {
        require_once __DIR__ . '/../src/auth/signup.php';
    }

    public static function login()
    {
        require_once __DIR__ . '/../src/auth/login.php';
    }
}