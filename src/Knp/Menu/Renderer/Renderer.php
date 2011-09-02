<?php

namespace Knp\Menu\Renderer;

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}

abstract class Renderer
{
    /**
     * Whether or not to render menus with pretty spacing, or fully compressed.
     */
    protected $renderCompressed = false;

    protected $charset = 'UTF-8';

    /**
     * @param string $charset
     * @param boolean $renderCompressed
     */
    public function __construct($charset = null, $renderCompressed = false)
    {
        if (null !== $charset) {
            $this->charset = (string) $charset;
        }
        $this->renderCompressed = (boolean) $renderCompressed;
    }

    /**
     * Renders a HTML attribute
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    public function renderHtmlAttribute($name, $value)
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
    public function renderHtmlAttributes(array $attributes)
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
    public function escape($value)
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
     * Gets whether to render compressed HTML or not
     *
     * @return boolean
     */
    public function getRenderCompressed()
    {
        return $this->renderCompressed;
    }

    /**
     * Set whether to render compressed HTML or not
     *
     * @param boolean $bool
     */
    public function setRenderCompressed($bool)
    {
        $this->renderCompressed = (boolean) $bool;
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
