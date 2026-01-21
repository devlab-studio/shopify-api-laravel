<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

class BuildGraphQl
{
    protected string $resource;
    protected array $with = [];
    protected array $limits = [];

    public function __construct(string $resource)
    {
        $this->resource = $resource;
    }

    public function with(array $relations): self
    {
        $this->with = $relations;
        return $this;
    }

    public function limits(array $limits): self
    {
        $this->limits = $limits;
        return $this;
    }

    public function build(): string
    {
        $fullConfig = config("shopify-api-laravel");

        $fields = $fullConfig[$this->resource] ?? [];


        $withs = [];
        foreach ($this->with as $relation) {
            $withs[$relation] = true;
        }

        $content = $this->parseConfig($fields, $fullConfig, $withs, '');

        $normalizedLimits = [];
        foreach ($this->limits as $key => $value) {
            $normalizedLimits[$key] = $value;
        }

        foreach ($normalizedLimits as $key => $value) {
            $content = str_replace("##{$key}Count##", $value, $content);

            $shortKey = str_contains($key, '.') ? substr($key, strrpos($key, '.') + 1) : $key;
            $content = str_replace("##{$shortKey}Count##", $value, $content);
        }

        $content = preg_replace('/##\w+Count##/', '0', $content);


        do {
            $before = $content;

            $content = preg_replace('/[a-zA-Z_]\w*\s*\([^)]+\)\s*\{\s*nodes\s*(?:\{\s*\})?\s*\}/s', '', $content);

            $content = preg_replace('/\{\s*\}/', '', $content);

            $content = preg_replace('/^\s*[a-zA-Z_]\w*\s*\([^)]+\)\s*$/m', '', $content);
        } while ($before !== $content);

        $content = preg_replace('/\n\s*\n+/', "\n", $content);
        $content = trim($content);

        // dd('<pre>' . htmlspecialchars($content) . '</pre>');
        return $content;
    }

    protected function parseConfig($content, array $fullConfig, array $withs, string $parentPath = ''): string
    {
        if (is_array($content)) {
            $content = implode("\n", $content);
        }

        return preg_replace_callback('/##([^#]+)Nodes##/', function ($matches) use ($content, $fullConfig, $withs, $parentPath) {

            $key = $matches[1];
            $fullKey = $parentPath ? "{$parentPath}.{$key}" : $key;

            if (isset($withs[$fullKey]) || isset($withs[$key])) {
                $parsedContent = $this->parseConfig($fullConfig[$key] ?? [], $fullConfig, $withs, $fullKey);

                $parsedContent = $this->processNestedFields($parsedContent, $fullConfig, $withs, $fullKey);

                return $parsedContent;
            } else {
                return '';
            }

        }, $content);
    }

    protected function processNestedFields(string $content, array $fullConfig, array $withs, string $parentPath): string
    {
        return preg_replace_callback('/##([^#]+)##/', function ($matches) use ($content, $fullConfig, $withs, $parentPath) {

            $key = $matches[1];
            $key = str_replace('Nodes', '', $key);
            $fullKey = "{$parentPath}.{$key}";

            if (isset($fullConfig[$fullKey])) {
                return $this->parseConfig($fullConfig[$fullKey], $fullConfig, $withs, $fullKey);
            } else if (isset($fullConfig[$key])) {
                return $this->parseConfig($fullConfig[$key], $fullConfig, $withs, $fullKey);
            }

            return $matches[0];

        }, $content);
    }
}
