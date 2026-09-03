<?php

namespace App\Support\Cms;

class StatCardPresenter
{
    /**
     * @param  array<int, array{key: string, label: string, hint?: string, icon?: string, color?: string}>  $definitions
     * @param  array<string, int|float|string|null>  $metrics
     * @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}>
     */
    public function present(array $definitions, array $metrics): array
    {
        return array_map(function (array $definition) use ($metrics) {
            $value = $metrics[$definition['key']] ?? 0;

            return [
                'label' => $definition['label'],
                'value' => is_numeric($value) ? number_format((float) $value) : (string) $value,
                'hint' => $definition['hint'] ?? null,
                'icon' => $definition['icon'] ?? 'ti-chart-bar',
                'color' => $definition['color'] ?? 'primary',
            ];
        }, $definitions);
    }
}
