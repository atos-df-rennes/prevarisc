<?php

class Cache_UnserializedMemcache extends Memcache
{
    public function get($key, $flags = null)
    {
        return $this->fixReturnedContent(parent::get($key, $flags));
    }

    protected function fixReturnedContent($data)
    {
        if (false === $data) {
            return false;
        }

        return is_string($data) ? unserialize($data) : $data;
    }
}
