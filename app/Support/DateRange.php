<?php

namespace App\Support;

use Carbon\Carbon;

class DateRange
{
    /**
     * Presets disponibles. Cada uno resuelve a un rango [from, to] en formato Y-m-d.
     *
     * @return array<string, string> ['from' => 'Y-m-d', 'to' => 'Y-m-d']
     */
    public static function preset(string $name): array
    {
        $now = Carbon::now();

        return match ($name) {
            'today' => ['from' => $now->toDateString(), 'to' => $now->toDateString()],
            'yesterday' => ['from' => $now->copy()->subDay()->toDateString(), 'to' => $now->copy()->subDay()->toDateString()],
            'this_week' => ['from' => $now->copy()->startOfWeek()->toDateString(), 'to' => $now->copy()->endOfWeek()->toDateString()],
            'last_week' => [
                'from' => $now->copy()->subWeek()->startOfWeek()->toDateString(),
                'to' => $now->copy()->subWeek()->endOfWeek()->toDateString(),
            ],
            'this_month' => ['from' => $now->copy()->startOfMonth()->toDateString(), 'to' => $now->copy()->endOfMonth()->toDateString()],
            'last_month' => [
                'from' => $now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                'to' => $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString(),
            ],
            'this_quarter' => ['from' => $now->copy()->startOfQuarter()->toDateString(), 'to' => $now->copy()->endOfQuarter()->toDateString()],
            'last_quarter' => [
                'from' => $now->copy()->subQuarter()->startOfQuarter()->toDateString(),
                'to' => $now->copy()->subQuarter()->endOfQuarter()->toDateString(),
            ],
            'this_year' => ['from' => $now->copy()->startOfYear()->toDateString(), 'to' => $now->copy()->endOfYear()->toDateString()],
            'last_year' => [
                'from' => $now->copy()->subYear()->startOfYear()->toDateString(),
                'to' => $now->copy()->subYear()->endOfYear()->toDateString(),
            ],
            'all' => ['from' => '1900-01-01', 'to' => '2999-12-31'],
            default => ['from' => '', 'to' => ''],
        };
    }

    /**
     * Catálogo de presets para renderizar la barra de botones.
     * 'all' se trata como "sin filtro de fecha".
     *
     * @return array<string, string> [slug => etiqueta]
     */
    public static function catalog(): array
    {
        return [
            'today' => 'Hoy',
            'yesterday' => 'Ayer',
            'this_week' => 'Semana',
            'last_week' => 'Sem. pasada',
            'this_month' => 'Mes',
            'last_month' => 'Mes pasado',
            'this_quarter' => 'Trimestre',
            'last_quarter' => 'Trim. pasado',
            'this_year' => 'Año',
            'last_year' => 'Año pasado',
            'all' => 'Todo',
        ];
    }

    /**
     * Detecta qué preset coincide con los valores from/to actuales.
     * Devuelve el slug del preset o null si es un rango personalizado.
     */
    public static function detectActive(?string $from, ?string $to): ?string
    {
        $from = (string) $from;
        $to = (string) $to;

        if ($from === '' && $to === '') {
            return 'all';
        }

        foreach (self::catalog() as $slug => $_label) {
            if ($slug === 'all') {
                continue;
            }
            $range = self::preset($slug);
            if ($range['from'] === $from && $range['to'] === $to) {
                return $slug;
            }
        }

        return null; // rango personalizado
    }
}
