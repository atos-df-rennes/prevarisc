<?php

class Service_PlatauClient
{
    private $options = [];

    public function __construct()
    {
        $this->options[CURLOPT_RETURNTRANSFER] = 1;

        if (filter_var(getenv('PREVARISC_PROXY_ENABLED'), FILTER_VALIDATE_BOOLEAN)) {
            $this->options[CURLOPT_PROXYTYPE] = getenv('PREVARISC_PROXY_PROTOCOL');
            $this->options[CURLOPT_PROXYPORT] = getenv('PREVARISC_PROXY_PORT');
            $this->options[CURLOPT_PROXY] = getenv('PREVARISC_PROXY_HOST');

            if (getenv('PREVARISC_PROXY_USERNAME')) {
                $this->options[CURLOPT_PROXYUSERPWD] = getenv('PREVARISC_PROXY_USERNAME').':'.getenv('PREVARISC_PROXY_PASSWORD');
            }
        }
    }

    /**
     * @param int              $name
     * @param array|int|string $value
     */
    public function addOption($name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
