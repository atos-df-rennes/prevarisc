<?php

use PHPUnit\Framework\TestCase;

final class PassAuthAdapterTest extends TestCase
{
    public function testAuthenticationSuccess()
    {
        $this->assertSame(1, (new Service_PassAuthAdapater('username'))->authenticate()->getCode());
    }
}