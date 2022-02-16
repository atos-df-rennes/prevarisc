<?php

/**
 * Fake authentication.
 *
 * @author a511701
 */
class Service_PassAuthAdapater implements Zend_Auth_Adapter_Interface
{
    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function authenticate()
    {
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->username);
    }
}
