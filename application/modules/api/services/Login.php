<?php

class Api_Service_Login
{
    /**
     * login.
     *
     * @param null|mixed $username
     * @param null|mixed $password
     *
     * @return array
     */
    public function login($username = null, $password = null)
    {
        $Service_login = new Service_Login();

        return $Service_login->login($username, $password);
    }
}
