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

    /**
     * Especifica las relaciones a incluir en la query, similar a Laravel's with()
     * Soporta notación de punto para relaciones anidadas y circulares:
     * ['products', 'product.variants', 'variants.product']
     */
    public function with(array $relations): self
    {
        $normalized = [];
        foreach ($relations as $relation) {

            if (strpos($relation, $this->resource . '.') === 0) {
                $relation = substr($relation, strlen($this->resource) + 1);
            }
            if (!empty($relation)) {
                $normalized[] = $relation;
            }
        }
        $this->with = $normalized;
        return $this;
    }

    /**
     * Especifica los límites de paginación para relaciones
     */
    public function limits(array $limits): self
    {
        $this->limits = $limits;
        return $this;
    }

    /**
     * Construye la query GraphQL basada en la configuración y relaciones especificadas
     */
    public function build(): string
    {
        $fullConfig = config("shopify-api-laravel");
        $fields = $fullConfig[$this->resource] ?? [];

        $withs = $this->buildRelationshipTree($this->with);

        $hasSpecifiedRelations = !empty($this->with);

        $content = $this->parseConfig($fields, $fullConfig, $withs, '', 0, $hasSpecifiedRelations);

        $content = $this->cleanQuery($content);

        return $content;
    }

    /**
     * Construye un árbol jerárquico de relaciones con información de profundidad
     * Permite relaciones circulares pero evita profundidad infinita
     */
    protected function buildRelationshipTree(array $relations): array
    {
        $tree = [];

        foreach ($relations as $relation) {
            $parts = explode('.', $relation);
            $current = &$tree;

            foreach ($parts as $index => $part) {
                if (!isset($current[$part])) {
                    $current[$part] = ['_children' => []];
                }

                if ($index === count($parts) - 1) {
                    $current[$part]['_included'] = true;
                }

                $current = &$current[$part]['_children'];
            }
        }

        return $tree;
    }

    /**
     * Determina si una relación debe incluirse basándose en el árbol de relaciones
     * Controla la profundidad para evitar ciclos infinitos en relaciones circulares
     */
    protected function shouldIncludeRelation(string $key, array $withs, string $parentPath, int $depth, bool $hasSpecifiedRelations): bool
    {
        $maxDepth = 5;
        if ($depth > $maxDepth) {
            return false;
        }

        if (!$hasSpecifiedRelations) {
            return true;
        }

        if (empty($withs)) {
            return false;
        }

        if (isset($withs[$key])) {
            return isset($withs[$key]['_included']) || !empty($withs[$key]['_children']);
        }

        return false;
    }

    /**
     * Aplica los límites de paginación con el contexto de la ruta actual
     */
    protected function applyLimitsWithContext(string $content, string $currentPath): string
    {
        return preg_replace_callback('/##(\w+)Count##/', function($matches) use ($currentPath) {
            $placeholder = $matches[1];

            $fullPath = $currentPath ? "{$currentPath}.{$placeholder}" : $placeholder;

            if (isset($this->limits[$fullPath])) {
                return $this->limits[$fullPath];
            }

            return '0';
        }, $content);
    }

    /**
     * Limpia la query eliminando campos vacíos y formateando
     */
    protected function cleanQuery(string $content): string
    {
        do {
            $before = $content;

            $content = $this->removeFieldsWithZeroLimit($content);

            $content = preg_replace('/[a-zA-Z_]\w*\s*(?:\([^)]+\))?\s*\{\s*\}/', '', $content);

            $content = preg_replace('/[a-zA-Z_]\w*\s*\([^)]+\)\s*\{\s*nodes\s*\{\s*\}\s*\}/s', '', $content);

            $content = preg_replace('/^\s*[a-zA-Z_]\w*\s*\([^)]+\)\s*$/m', '', $content);
        } while ($before !== $content);

        // Normalizar espacios en blanco
        $content = preg_replace('/\n\s*\n+/', "\n", $content);
        $content = trim($content);

        return $content;
    }

    /**
     * Elimina campos con first: 0 manejando llaves anidadas correctamente
     */
    protected function removeFieldsWithZeroLimit(string $content): string
    {
        $result = '';
        $i = 0;
        $length = strlen($content);

        while ($i < $length) {

            $pattern = '/[a-zA-Z_]\w*\s*\([^)]*?:\s*0[^)]*?\)\s*\{/';

            if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, $i)) {
                $matchPos = $matches[0][1];

                if ($matchPos > $i) {
                    $result .= substr($content, $i, $matchPos - $i);
                }


                $i = $matchPos + strlen($matches[0][0]);


                $braceCount = 1;
                while ($i < $length && $braceCount > 0) {
                    if ($content[$i] === '{') {
                        $braceCount++;
                    } elseif ($content[$i] === '}') {
                        $braceCount--;
                    }
                    $i++;
                }
            } else {

                $result .= substr($content, $i);
                break;
            }
        }

        return $result;
    }
    protected function parseConfig($content, array $fullConfig, array $withs, string $parentPath = '', int $depth = 0, bool $hasSpecifiedRelations = false): string
    {
        if (is_array($content)) {
            $content = implode("\n", $content);
        }

        $content = $this->applyLimitsWithContext($content, $parentPath);

        return preg_replace_callback('/##([^#]+)Nodes##/', function ($matches) use ($content, $fullConfig, $withs, $parentPath, $depth, $hasSpecifiedRelations) {

            $key = $matches[1];
            $fullKey = $parentPath ? "{$parentPath}.{$key}" : $key;

            if ($this->shouldIncludeRelation($key, $withs, $parentPath, $depth, $hasSpecifiedRelations)) {
                $nextWiths = isset($withs[$key]['_children']) ? $withs[$key]['_children'] : [];

                $parsedContent = $this->parseConfig($fullConfig[$key] ?? [], $fullConfig, $nextWiths, $fullKey, $depth + 1, $hasSpecifiedRelations);

                $parsedContent = $this->processNestedFields($parsedContent, $fullConfig, $nextWiths, $fullKey, $depth + 1, $hasSpecifiedRelations);

                return $parsedContent;
            } else {
                return '';
            }

        }, $content);
    }

    /**
     * Procesa campos anidados dentro de una relación
     * Mantiene control de profundidad
     */
    protected function processNestedFields(string $content, array $fullConfig, array $withs, string $parentPath, int $depth = 0, bool $hasSpecifiedRelations = false): string
    {
        return preg_replace_callback('/##([^#]+)##/', function ($matches) use ($content, $fullConfig, $withs, $parentPath, $depth, $hasSpecifiedRelations) {

            $key = $matches[1];
            $key = str_replace('Nodes', '', $key);
            $fullKey = "{$parentPath}.{$key}";

            if (isset($fullConfig[$fullKey])) {
                return $this->parseConfig($fullConfig[$fullKey], $fullConfig, $withs, $fullKey, $depth, $hasSpecifiedRelations);
            } else if (isset($fullConfig[$key])) {
                return $this->parseConfig($fullConfig[$key], $fullConfig, $withs, $fullKey, $depth, $hasSpecifiedRelations);
            }

            return $matches[0];

        }, $content);
    }
}
