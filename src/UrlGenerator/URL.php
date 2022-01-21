<?php

namespace Farzai\PhoneVerification\UrlGenerator;

class URL
{
    /**
     * @var array
     */
    private array $attributes;

    /**
     * @param string $url
     * @return URL
     */
    public static function parse(string $url): URL
    {
        $result = parse_url($url);

        $scheme = $result['scheme'] ?? "http";
        $host = $result['host'];
        $port = $result['port'] ?? null;
        $path = $result['path'] ?? null;

        return new static([
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
            'path' => $path,
            'query' => static::parseQuery($url)
        ]);
    }

    /**
     * Parse query and return key=value
     *
     * @param string $url
     * @return array
     */
    public static function parseQuery(string $url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);

        $query = array_filter(explode("&", $queryString));

        $currentQuery = array_map(fn ($val) => explode("=", $val), $query);
        $keyValues = [];
        foreach ($currentQuery as [$key, $value]) {
            $keyValues[$key] = $value;
        }

        return $keyValues;
    }


    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function append($key, $value = null)
    {
        $values = is_array($key) ? $key : [$key => $value];

        foreach ($values as $name => $val) {
            $this->attributes['query'][$name] = $val;
        }

        return $this;
    }


    public function build()
    {
        $scheme = $this->attributes['scheme'];
        $port = $this->attributes['port'] ? ":{$this->attributes['port']}" : "";

        $query = http_build_query($this->attributes['query']);

        return "{$scheme}://{$this->attributes['host']}{$port}{$this->attributes['path']}?{$query}";
    }


    /**
     * @param array $attributes
     */
    private function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}