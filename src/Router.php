<?php

namespace FuckingSmall;

/**
 * A very simple router
 *
 * @package FuckingSmall
 */
class Router implements RouterInterface
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var array
     */
    private $defaultFilters = [];

    /**
     *
     */
    public function __construct()
    {
        $this->defaultFilters = [
            '{default}' => '[a-zA-Z0-9_\+\-%]+',
            '{gobble}'  => '[a-zA-Z0-9_\+\-%\/]+',
            '{int}'     => '[0-9]+',
            '{alpha}'   => '[a-zA-Z]+',
            '{slug}'    => '[a-zA-Z0-9_-]+'
        ];
    }

    /**
     * @param string $name
     * @param string $rule
     * @param string $action
     * @param array  $options
     *
     * @return $this
     */
    public function attach($name, $rule, $action, array $options = [])
    {
        $this->routes[$name] = [
            'rule'    => $rule,
            'action'  => $action,
            'options' => $options
        ];

        return $this;
    }

    /**
     * Attempt to resolve a route from a URL
     *
     * @param Request $request
     *
     * @return array|false
     */
    public function resolve(Request $request)
    {
        foreach ($this->routes as $name => $route) {
            $options = $route['options'];
            $regex   = $this->compileRegex($route['rule'], $options);
            $tokens  = $this->compileTokens($route['rule']);

            $results = $this->compileResults($regex, $tokens, $request->getUri(), $options);

            if ($results !== false) {
                $payload = [];
                list($controller, $method) = explode('::', $route['action']);
                $payload['_controller']    = $controller;
                $payload['_method']        = $method;
                $payload['_route']         = $name;

                $payload = array_merge($results, $payload);

                return $payload;
            }
        }

        return false;
    }

    /**
     * @param       $name
     * @param array $params
     *
     * @return string
     */
    public function gentUrl($name, array $params = [])
    {
        if (array_key_exists($name, $this->routes)) {
            $route   = $this->routes[$name];
            $options = $route['options'];
            $tokens  = $this->compileTokens($route['rule']);

            $results = [];
            foreach ($tokens as $index => $rawValue) {
                $value = str_replace(['{', '}'], '', $rawValue);

                // Save defaults
                if (array_key_exists('defaults', $options) && array_key_exists($value, $options['defaults'])) {
                    $results[$rawValue] = $options['defaults'][$value];
                } elseif (array_key_exists($value, $params)) {
                    $results[$rawValue] = $params[$value];
                } else {
                    throw new \RuntimeException(sprintf(
                        "No '%s' parameter passed to the url generation for '%s'",
                        $value,
                        $name
                    ));
                }
            }

            return str_replace(array_keys($results), array_values($results), $route['rule']);
        }
    }

    /**
     * Build a regular expression from the given rule.
     *
     * @param string $rule
     * @param array $options
     *
     * @return string
     */
    private function compileRegex($rule, array $options = [])
    {
        $regex = '^' . preg_replace_callback(
            '@\{[\w]+\}@',
            function ($matches) use ($options) {
                $optional = false;
                $key      = str_replace(['{', '}'], '', $matches[0]);

                if (array_key_exists('defaults', $options) && array_key_exists($key, $options['defaults'])) {
                    $optional = true;
                }

                if (array_key_exists('filters', $options) && array_key_exists($key, $options['filters'])) {
                    if (array_key_exists($options['filters'][$key], $this->defaultFilters)) {
                        return  ($optional ? '?' : '') . '(' . $this->defaultFilters[$options['filters'][$key]] . ')' . ($optional ? '?' : '');
                    } else {
                        return ($optional ? '?' : '') . '(' . $options['filters'][$key] . ')' . ($optional ? '?' : '');
                    }
                } else {
                    return ($optional ? '?' : '') . '(' . $this->defaultFilters['{default}'] . ')' . ($optional ? '?' : '');
                }
            },
            $rule
        ) . '$';

        return $regex;
    }

    /**
     * Find tokens within given rule.
     *
     * @param string $rule
     */
    private function compileTokens($rule)
    {
        $tokens = [];
        preg_match_all('@\{([\w]+)\}@', $rule, $tokens, PREG_PATTERN_ORDER);

        return $tokens[0];
    }

    /**
     * Match a regular expression against a given *haystack* string. Returning the resulting matches indexed
     * by the values of the given tokens.
     *
     * @param string $regex
     * @param array  $tokens
     * @param string $haystack
     * @param array  $options
     *
     * @return array|false
     */
    private function compileResults($regex, $tokens, $haystack, array $options = [])
    {
        $results = [];

        // Test the regular expression against the supplied *haystack* string.
        if (preg_match('@' . $regex . '@', $haystack, $values)) {
            // Discard *all* matches index.
            array_shift($values);

            // Match tokens to values.
            foreach ($tokens as $index => $value) {
                $value = str_replace(['{', '}'], '', $value);

                // Save defaults
                if (array_key_exists('defaults', $options)) {
                    $defaults = $options['defaults'];
                    if (array_key_exists($value, $defaults)) {
                        $results[$value] = $defaults[$value];
                    }
                }

                // Save parsed values, overriding defaults if necessary
                if (array_key_exists($index, $values)) {
                    $results[$value] = $values[$index];
                }
            }

            return $results;
        }

        return false;
    }
}