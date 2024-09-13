<?php

class Api_Service_Login
{
    /**
     * login.
     *
     * @param null|mixed $username
     * @param null|mixed $password
     */
    public function login(?string $username = null, ?string $password = null): array
    {
        $Service_login = new Service_Login();

        return $Service_login->login($username, $password);
    }
}
