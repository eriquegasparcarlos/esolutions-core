<?php

namespace App\ESolutions\DataTable\Table;

/**
 * Clase para definir un filtro configurable para tablas del frontend.
 */
class Filter implements \JsonSerializable
{
    /** @var string|null */
    protected $label = null;
    /** @var string|null */
    protected $name = null;
    /** @var string|null */
    protected $type = null;
    /** @var array|null */
    protected $options = null;
    /** @var string|null */
    protected $default = null;
    /** @var mixed */
    public $value = null;
    /** @var bool */
    protected $includeAllOption = false;
    /** @var string|null */
    protected $class = null;
    /** @var string */
    protected $dateStart = '';
    /** @var string */
    protected $dateEnd = '';
    /** @var string */
    protected $monthStart = '';
    /** @var string */
    protected $monthEnd = '';
    /** @var bool */
    protected $filterLocal = false;

    // --- XTreeSelect ---
    /** @var bool */
    protected $withFilter = false;
    /** @var bool */
    protected $multiple = false;
    /** @var bool */
    protected $onlyLeafSelectable = false;
    /** @var string */
    protected $optionValue = 'id';
    /** @var string */
    protected $optionLabel = 'label';
    /** @var string */
    protected $optionChildren = 'children';

    /** @var string|null */
    protected $dependsOn = null;
    /** @var array|null */
    protected $remote = null;
    /** @var bool */
    protected $resetOnParentChange = true;
    /** @var bool */
    protected $disableWhenParentEmpty = true;
    /** @var bool */
    protected $clearable = false;
    /** @var bool */
    protected $filterable = false;
    /** @var string|null */
    protected $searchUrl = null;

    protected function __construct() {}

    /**
     * @param string $name
     * @return self
     */
    public static function make($name)
    {
        $instance = new self();
        $instance->name = $name;
        return $instance;
    }

    /**
     * @param string $label
     * @return self
     */
    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $type
     * @return self
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param array $options
     * @return self
     */
    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string|null $default
     * @return self
     */
    public function default($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $dateStart
     * @return self
     */
    public function dateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param string $dateEnd
     * @return self
     */
    public function dateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @param string $monthStart
     * @return self
     */
    public function monthStart($monthStart)
    {
        $this->monthStart = $monthStart;
        return $this;
    }

    /**
     * @param string $monthEnd
     * @return self
     */
    public function monthEnd($monthEnd)
    {
        $this->monthEnd = $monthEnd;
        return $this;
    }

    /**
     * @param bool $include
     * @return self
     */
    public function includeAllOption($include = true)
    {
        $this->includeAllOption = $include;
        if ($include && in_array($this->type, ['select', 'tree-select'], true)) {
            $this->value = 'all';
        }
        return $this;
    }

    /**
     * @param bool $filterLocal
     * @return self
     */
    public function filterLocal($filterLocal = false)
    {
        $this->filterLocal = $filterLocal;
        return $this;
    }

    // -------------------------
    // XTreeSelect helpers
    // -------------------------

    /**
     * @param bool $v
     * @return self
     */
    public function withFilter($v = true)
    {
        $this->withFilter = $v;
        return $this;
    }

    /**
     * @param bool $v
     * @return self
     */
    public function multiple($v = true)
    {
        $this->multiple = $v;
        return $this;
    }

    /**
     * @param bool $v
     * @return self
     */
    public function onlyLeafSelectable($v = true)
    {
        $this->onlyLeafSelectable = $v;
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function optionValue($key)
    {
        $this->optionValue = $key;
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function optionLabel($key)
    {
        $this->optionLabel = $key;
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function optionChildren($key)
    {
        $this->optionChildren = $key;
        return $this;
    }

    /**
     * @param bool $v
     * @return self
     */
    public function clearable($v = true)
    {
        $this->clearable = $v;
        return $this;
    }

    /**
     * @param bool $v
     * @return self
     */
    public function filterable($v = true)
    {
        $this->filterable = $v;
        return $this;
    }

    /**
     * @param string $url
     * @return self
     */
    public function searchUrl($url)
    {
        $this->searchUrl = $url;
        $this->filterable = true;
        return $this;
    }

    /**
     * @param string $class
     * @return self
     */
    public function cssClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $options = $this->options;

        // Para XTreeSelect no existe 'includeAllOption' nativo como en XSelect.
        // Si se solicita, envolvemos el árbol dentro de un nodo raíz "All".
        if ($this->includeAllOption && $this->type === 'tree-select') {
            $options = [
                [
                    $this->optionValue => 'all',
                    $this->optionLabel => __('all'),
                    $this->optionChildren => is_array($options) ? $options : [],
                    'selectable' => true,
                ]
            ];
        }

        return [
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'options' => $options,
            'default' => $this->default,
            'value' => $this->value,
            'includeAllOption' => $this->includeAllOption,
            'class' => $this->class,
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
            'monthStart' => $this->monthStart,
            'monthEnd' => $this->monthEnd,
            'filterLocal' => $this->filterLocal,

            // XTreeSelect props
            'withFilter' => $this->withFilter,
            'multiple' => $this->multiple,
            'onlyLeafSelectable' => $this->onlyLeafSelectable,
            'optionValue' => $this->optionValue,
            'optionLabel' => $this->optionLabel,
            'optionChildren' => $this->optionChildren,

            'clearable' => $this->clearable,
            'filterable' => $this->filterable,
            'searchUrl' => $this->searchUrl,
            'dependsOn' => $this->dependsOn,
            'remote' => $this->remote,
            'resetOnParentChange' => $this->resetOnParentChange,
            'disableWhenParentEmpty' => $this->disableWhenParentEmpty,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $class
     * @return self
     */
    public static function makeInput($name, $label = '', $class = 'col-6')
    {
        return self::make($name)
            ->label($label)
            ->type('input')
            ->default('')
            ->cssClass($class);
    }

    /**
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $class
     * @return self
     */
    public static function makeSelect($name, $label = '', array $options = [], $class = 'col-6')
    {
        return self::make($name)
            ->label($label)
            ->type('select')
            ->options($options)
            ->optionLabel('name')
            ->default('all')
            ->includeAllOption()
            ->cssClass($class);
    }

    /**
     * Filtro predefinido tipo tree-select (XTreeSelect).
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $class
     * @return self
     */
    public static function makeTreeSelect($name, $label = '', array $options = [], $class = 'col-6')
    {
        return self::make($name)
            ->label($label)
            ->type('tree-select')
            ->options($options)
            ->default('all')
            ->includeAllOption()
            ->withFilter(true)
            ->cssClass($class);
    }

    /**
     * Filtro select con búsqueda remota al backend.
     *
     * @param string $name
     * @param string $label
     * @param string $url
     * @param string $class
     * @return self
     */
    public static function makeSearch($name, $label, $url, $class = 'col-6')
    {
        return self::make($name)
            ->label($label)
            ->type('select')
            ->options([])
            ->optionValue('id')
            ->optionLabel('name')
            ->searchUrl($url)
            ->clearable()
            ->cssClass($class);
    }

    /**
     * @param string $name
     * @param string|null $label
     * @return self
     */
    public static function makePeriod($name = 'period', $label = null, $class = 'col-24')
    {
        $periodOptions = [
            ['id' => 'month', 'name' => __('by month')],
            ['id' => 'date', 'name' => __('by date')],
            ['id' => 'between_months', 'name' => __('between month')],
            ['id' => 'between_dates', 'name' => __('between date')],
        ];

        $instance = self::make($name)
            ->label($label !== null ? $label : __('period'))
            ->type('date')
            ->options($periodOptions)
            ->value('month')
            ->dateStart(date('Y-m-d'))
            ->dateEnd(date('Y-m-d'))
            ->monthStart(date('Y-m'))
            ->monthEnd(date('Y-m'));

        if ($class) {
            $instance->cssClass($class);
        }

        return $instance;
    }

    /**
     * @param string $parentName
     * @return self
     */
    public function dependsOn($parentName)
    {
        $this->dependsOn = $parentName;
        return $this;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @return self
     */
    public function remoteOptions($url, $method = 'get', array $params = [])
    {
        $this->remote = [
            'url' => $url,
            'method' => strtolower($method),
            'params' => $params,
        ];
        return $this;
    }

    /**
     * @param bool $v
     * @return self
     */
    public function resetOnParentChange($v = true)
    {
        $this->resetOnParentChange = $v;
        return $this;
    }

    /**
     * @param bool $v
     * @return self
     */
    public function disableWhenParentEmpty($v = true)
    {
        $this->disableWhenParentEmpty = $v;
        return $this;
    }
}
