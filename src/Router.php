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
    private $options = [];

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
     * @param string $rule
     * @param string $action
     * @param array  $options
     *
     * @return $this
     */
    public function attach($rule, $action, array $options = [])
    {
        $this->routes[$rule] = $action;
        $this->options[$rule] = $options;

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
        foreach ($this->routes as $rule => $action) {
            $regex   = $this->compileRegex($rule);
            $tokens  = $this->compileTokens($rule);

            $results = $this->compileResults($regex, $tokens, $request->getUri());

            if ($results !== false) {
                $payload = [];
                list($controller, $method) = explode('::', $action);
                $payload['_controller']    = $controller;
                $payload['_method']        = $method;
                $payload['_route']         = $rule;

                $payload = array_merge($results, $payload);

                return $payload;
            }
        }

        return false;
    }

    /**
     * Build a regular expression from the given rule.
     *
     * @param string $rule
     *
     * @return string
     */
    private function compileRegex($rule)
    {
        $options = $this->options[$rule];

        $regex = '^' . preg_replace_callback(
            '@\{[\w]+\}@',
            function ($matches) use ($options) {
                $key = str_replace(['{', '}'], '', $matches[0]);
                if (array_key_exists('filters', $options) && array_key_exists($key, $options['filters'])) {
                    if (array_key_exists($options['filters'][$key], $this->defaultFilters)) {
                        return '(' . $this->defaultFilters[$options['filters'][$key]] . ')';
                    } else {
                        return '(' . $options['filters'][$key] . ')';
                    }
                } else {
                    return '(' . $this->defaultFilters['{default}'] . ')';
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
     * @param array $tokens
     * @param string $haystack
     *
     * @return array|false
     */
    private function compileResults($regex, $tokens, $haystack)
    {
        $results = [];

        // Test the regular expression against the supplied *haystack* string.
        if (preg_match('@' . $regex . '@', $haystack, $values)) {
            // Discard *all* matches index.
            array_shift($values);
            // Match tokens to values found by the regex.
            foreach ($tokens as $index => $value) {
                if (isset($values[$index])) {
                    $results[str_replace(['{', '}'], '', $value)] = urldecode($values[$index]);
                }
            }

            return $results;
        }

        return false;
    }
}