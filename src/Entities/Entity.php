<?php

namespace Farzai\PhoneVerification\Entities;

use ArrayAccess;
use JsonSerializable;

class Entity implements ArrayAccess, JsonSerializable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return $this->data[$name] ?? null;
    }


    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return;
        }

        $this->data[$name] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->data[$offset] ?? false) {
            unset($this->data[$offset]);
        }
    }

    public function toArray()
    {
        return $this->data;
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}