<?php

namespace App\Helpers;

if (!function_exists('get_status_badge')) {
    /**
     * Get HTML badge for sensor reading status
     *
     * @param string $status Status value
     * @param bool $withIcon Whether to include icon
     * @return string HTML badge
     */
    function get_status_badge($status, $withIcon = false)
    {
        $status = strtolower(trim($status));
        
        $badges = [
            'normal' => [
                'class' => 'status-badge',
                'text' => 'Normal',
                'icon' => 'fa-check-circle'
            ],
            'good' => [
                'class' => 'status-badge',
                'text' => 'Good',
                'icon' => 'fa-check-circle'
            ],
            'warning' => [
                'class' => 'status-badge warning',
                'text' => 'Warning',
                'icon' => 'fa-exclamation-triangle'
            ],
            'danger' => [
                'class' => 'status-badge danger',
                'text' => 'Critical',
                'icon' => 'fa-times-circle'
            ],
            'critical' => [
                'class' => 'status-badge danger',
                'text' => 'Critical',
                'icon' => 'fa-times-circle'
            ],
            'no_data' => [
                'class' => 'status-badge',
                'text' => 'No Data',
                'icon' => 'fa-question-circle'
            ]
        ];
        
        $config = $badges[$status] ?? $badges['normal'];
        
        $iconHtml = '';
        if ($withIcon) {
            $iconHtml = '<i class="fas ' . $config['icon'] . ' me-1"></i>';
        }
        
        return '<span class="badge ' . $config['class'] . '">' . $iconHtml . $config['text'] . '</span>';
    }
}