<?php

namespace App\ESolutions\DataTable\Table;

use JsonSerializable;

/**
 * CellComponent: Represents an interactive cell rendered as a frontend component
 * (XToggle, XCheckbox, XInput, XSelect, etc.) with an optional action definition.
 */
class CellComponent implements JsonSerializable
{
    /** @var string */
    protected $component;
    /** @var mixed */
    protected $modelValue;
    /** @var array */
    protected $props = [];
    /** @var array|null */
    protected $action = null;

    /**
     * @param string $component
     * @param mixed $modelValue
     * @param array $props
     */
    public function __construct($component, $modelValue = null, array $props = [])
    {
        $this->component = $component;
        $this->modelValue = $modelValue;
        $this->props = $props;
    }

    /**
     * Generic factory.
     *
     * @param string $component
     * @param mixed $modelValue
     * @param array $props
     * @return self
     */
    public static function make($component, $modelValue = null, array $props = [])
    {
        return new self($component, $modelValue, $props);
    }

    // -------------------------
    // Component shortcuts
    // -------------------------

    /**
     * @param bool $checked
     * @param array $props
     * @return self
     */
    public static function toggle($checked, array $props = [])
    {
        return self::make('XToggle', $checked, $props);
    }

    /**
     * @param bool $checked
     * @param array $props
     * @return self
     */
    public static function checkbox($checked, array $props = [])
    {
        return self::make('XCheckbox', $checked, $props);
    }

    /**
     * @param mixed $value
     * @param array $props
     * @return self
     */
    public static function input($value, array $props = [])
    {
        return self::make('XInput', $value, $props);
    }

    /**
     * @param mixed $value
     * @param array $options
     * @param array $props
     * @return self
     */
    public static function select($value, array $options, array $props = [])
    {
        $props['options'] = $options;
        return self::make('XSelect', $value, $props);
    }

    // -------------------------
    // Props
    // -------------------------

    /**
     * @param mixed $value
     * @return self
     */
    public function modelValue($value)
    {
        $this->modelValue = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function prop($key, $value)
    {
        $this->props[$key] = $value;
        return $this;
    }

    /**
     * @param array $props
     * @return self
     */
    public function props(array $props)
    {
        $this->props = array_merge($this->props, $props);
        return $this;
    }

    // -------------------------
    // Actions
    // -------------------------

    /**
     * Define a generic API action that should run when the component value changes.
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param bool $refresh
     * @param bool $optimistic
     * @return self
     */
    public function api($method, $url, array $data = [], $refresh = true, $optimistic = true)
    {
        $this->action = [
            'type' => 'api',
            'method' => strtolower($method),
            'url' => $url,
            'data' => $data,
            'refresh' => $refresh,
            'optimistic' => $optimistic,
        ];
        return $this;
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $refresh
     * @param bool $optimistic
     * @return self
     */
    public function apiPatch($url, array $data = [], $refresh = true, $optimistic = true)
    {
        return $this->api('patch', $url, $data, $refresh, $optimistic);
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $refresh
     * @param bool $optimistic
     * @return self
     */
    public function apiPost($url, array $data = [], $refresh = true, $optimistic = true)
    {
        return $this->api('post', $url, $data, $refresh, $optimistic);
    }

    /**
     * Optional confirmation before executing action.
     *
     * @param string $title
     * @param string $message
     * @return self
     */
    public function confirm($title, $message)
    {
        if (!$this->action) {
            $this->action = ['type' => 'api'];
        }

        $this->action['confirm'] = [
            'title' => $title,
            'message' => $message,
        ];

        return $this;
    }

    /**
     * Optional debounce (ms) for inputs/selects.
     *
     * @param int $ms
     * @return self
     */
    public function debounce($ms)
    {
        if (!$this->action) {
            $this->action = ['type' => 'api'];
        }
        $this->action['debounce'] = $ms;
        return $this;
    }

    /**
     * @param bool $condition
     * @param \Closure $cb
     * @return self
     */
    public function when($condition, \Closure $cb)
    {
        if ($condition) {
            $cb($this);
        }
        return $this;
    }

    // -------------------------
    // Array / JSON
    // -------------------------

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = [
            'type_input' => 'component',
            'component' => $this->component,
            'modelValue' => $this->modelValue,
            'props' => $this->props,
        ];

        if ($this->action) {
            $arr['action'] = $this->action;
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
