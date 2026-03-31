<?php

namespace App\ESolutions\DataTable\Table;

/**
 * Clase Cell: Representa los distintos tipos de celdas que pueden ser renderizadas en el frontend.
 * Soporta estilos y componentes avanzados.
 */
class Cell
{
    /**
     * Texto plano, admite color, tamaño y negrita.
     *
     * @param string $text
     * @param string|null $color
     * @param string|null $size
     * @param bool|null $bold
     * @return array
     */
    public static function text($text, $color = null, $size = null, $bold = null)
    {
        $arr = [
            'type_input' => 'text',
            'value' => $text,
        ];
        if ($color) $arr['color'] = $color;
        if ($size) $arr['size'] = $size;
        if (!is_null($bold)) $arr['bold'] = $bold;
        return $arr;
    }

    /**
     * Badge (etiqueta colorida).
     *
     * @param string $label
     * @param string|null $color
     * @param string|null $type
     * @param bool $is_lighten_color
     * @return array
     */
    public static function badge($label, $color = null, $type = null, $is_lighten_color = true)
    {
        return [
            'type_input' => 'badge',
            'label' => $label,
            'color' => $color,
            'type' => $type,
            'is_lighten_color' => $is_lighten_color
        ];
    }

    /**
     * Ícono (ej: FontAwesome, Material).
     *
     * @param string $icon
     * @param string|null $color
     * @param string|null $tooltip
     * @return array
     */
    public static function icon($icon, $color = null, $tooltip = null)
    {
        $arr = [
            'type_input' => 'icon',
            'icon' => $icon,
        ];
        if ($color) $arr['color'] = $color;
        if ($tooltip) $arr['tooltip'] = $tooltip;
        return $arr;
    }

    /**
     * Link (enlace), opcional con ícono.
     *
     * @param string $label
     * @param string $url
     * @param string|null $target
     * @param string|null $icon
     * @return array
     */
    public static function link($label, $url, $target = null, $icon = null)
    {
        $arr = [
            'type_input' => 'link',
            'label' => $label,
            'url' => $url,
        ];
        if ($target) $arr['target'] = $target;
        if ($icon) $arr['icon'] = $icon;
        return $arr;
    }

    /**
     * Chip (tipo badge visual, ej: Quasar, Vuetify).
     *
     * @param string $label
     * @param string $color
     * @param string|null $icon
     * @return array
     */
    public static function chip($label, $color = 'primary', $icon = null)
    {
        $arr = [
            'type_input' => 'chip',
            'label' => $label,
            'color' => $color,
        ];
        if ($icon) $arr['icon'] = $icon;
        return $arr;
    }

    /**
     * Avatar (imagen de usuario o similar).
     *
     * @param string $src
     * @param string|null $alt
     * @param string|null $size
     * @return array
     */
    public static function avatar($src, $alt = null, $size = null)
    {
        $arr = [
            'type_input' => 'avatar',
            'src' => $src,
        ];
        if ($alt) $arr['alt'] = $alt;
        if ($size) $arr['size'] = $size;
        return $arr;
    }

    /**
     * Switch (toggle on/off).
     *
     * @param bool $checked
     * @param string|null $color
     * @param bool $readonly
     * @return array
     */
    public static function switchCell($checked, $color = null, $readonly = true)
    {
        $arr = [
            'type_input' => 'switch',
            'checked' => $checked,
            'readonly' => $readonly,
        ];
        if ($color) $arr['color'] = $color;
        return $arr;
    }

    /**
     * Celda compuesta: varias líneas y/o elementos combinados.
     *
     * @param array $lines
     * @return array
     */
    public static function composite(array $lines)
    {
        return [
            'type_input' => 'composite',
            'lines' => $lines,
        ];
    }

    /**
     * Multi-línea solo texto.
     *
     * @param array|string $lines
     * @return array
     */
    public static function multiLine($lines)
    {
        if (is_string($lines)) {
            $lines = explode("\n", $lines);
        }
        return [
            'type_input' => 'multi_line',
            'value' => $lines,
        ];
    }

    /**
     * Badge activo/inactivo.
     *
     * @param mixed $row
     * @param string $yesText
     * @param string $noText
     * @return array
     */
    public static function badgeIsActive($row, $yesText = 'Si', $noText = 'No')
    {
        $isActive = is_array($row)
            ? ($row['is_active'] ?? false)
            : ($row->is_active ?? false);

        return self::badge(
            $isActive ? $yesText : $noText,
            $isActive ? '#28c76f' : '#ff4c51'
        );
    }

    /**
     * @param mixed $value
     * @param string $yesText
     * @param string $noText
     * @return array
     */
    public static function badgeBoolean($value, $yesText = 'Si', $noText = 'No')
    {
        return self::badge(
            $value ? $yesText : $noText,
            $value ? '#28c76f' : '#ff4c51'
        );
    }

    /**
     * @param string $component
     * @param mixed $modelValue
     * @param array $props
     * @param array $action
     * @return array
     */
    public static function component($component, $modelValue, array $props = [], array $action = [])
    {
        return [
            'type_input' => 'component',
            'component' => $component,
            'modelValue' => $modelValue,
            'props' => $props,
            'action' => $action,
        ];
    }

    /**
     * @param bool $checked
     * @param array $action
     * @param array $props
     * @return array
     */
    public static function actionToggle($checked, array $action, array $props = [])
    {
        return self::component('XToggle', $checked, $props, $action);
    }

    /**
     * @param bool $checked
     * @param array $action
     * @param array $props
     * @return array
     */
    public static function actionCheckbox($checked, array $action, array $props = [])
    {
        return self::component('XCheckbox', $checked, $props, $action);
    }

    /**
     * @param mixed $value
     * @param array $action
     * @param array $props
     * @return array
     */
    public static function actionInput($value, array $action, array $props = [])
    {
        return self::component('XInput', $value, $props, $action);
    }

    /**
     * @param mixed $value
     * @param array $options
     * @param array $action
     * @param array $props
     * @return array
     */
    public static function actionSelect($value, array $options, array $action, array $props = [])
    {
        $props['options'] = $options;
        return self::component('XSelect', $value, $props, $action);
    }
}
