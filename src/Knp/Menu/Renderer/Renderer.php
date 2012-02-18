<?php

namespace Knp\Menu\Renderer;

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}

abstract class Renderer
{
    protected $charset = 'UTF-8';

    /**
     * @param string $charset
     */
    public function __construct($charset = null)
    {
        if (null !== $charset) {
            $this->charset = (string) $charset;
        }
    }

    /**
     * Renders a HTML attribute
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    protected function renderHtmlAttribute($name, $value)
    {
        if (true === $value) {
            return sprintf('%s="%s"', $name, $this->escape($name));
        }

        return sprintf('%s="%s"', $name, $this->escape($value));
    }

    /**
     * Renders HTML attributes
     *
     * @param array $attributes
     * @return string
     */
    protected function renderHtmlAttributes(array $attributes)
    {
        return implode('', array_map(array($this, 'htmlAttributesCallback'), array_keys($attributes), array_values($attributes)));
    }

    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * It removes empty attributes.
     *
     * @param  string $name   The attribute name
     * @param  string $value  The attribute value
     *
     * @return string The HTML representation of the HTML key attribute pair.
     */
    private function htmlAttributesCallback($name, $value)
    {
        if (false === $value || null === $value) {
            return '';
        }

        return ' '.$this->renderHtmlAttribute($name, $value);
    }

    /**
     * Escapes an HTML value
     *
     * @param string $value
     * @return string
     */
    protected function escape($value)
    {
        return $this->fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, $this->charset));
    }

    /**
     * Fixes double escaped strings.
     *
     * @param  string $escaped  string to fix
     * @return string A single escaped string
     */
    protected function fixDoubleEscape($escaped)
    {
        return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
    }

    /**
     * Get the HTML charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set the HTML charset
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = (string) $charset;
    }
}
