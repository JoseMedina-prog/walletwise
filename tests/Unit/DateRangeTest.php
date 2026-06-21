<?php

namespace Tests\Unit;

use App\Support\DateRange;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function test_previous_of_same_month_returns_previous_month(): void
    {
        Carbon::setTestNow('2026-06-15');
        // 1 jun - 30 jun (30 días) → prev_end = 31 may, prev_start = 2 may
        $prev = DateRange::previousOf('2026-06-01', '2026-06-30');
        $this->assertSame('2026-05-02', $prev['from']);
        $this->assertSame('2026-05-31', $prev['to']);
        Carbon::setTestNow();
    }

    public function test_previous_of_year_returns_previous_year(): void
    {
        $prev = DateRange::previousOf('2026-01-01', '2026-12-31');
        $this->assertSame('2025-01-01', $prev['from']);
        $this->assertSame('2025-12-31', $prev['to']);
    }

    public function test_previous_of_custom_range_returns_same_length(): void
    {
        $prev = DateRange::previousOf('2026-03-10', '2026-03-20');
        // 11 días (10-20 incluido) → previous_end = 2026-03-09, previous_start = 2026-02-27
        $this->assertSame('2026-03-09', $prev['to']);
        $this->assertSame('2026-02-27', $prev['from']);
    }

    public function test_previous_of_handles_30_days_across_months(): void
    {
        $prev = DateRange::previousOf('2026-04-15', '2026-05-14');
        $this->assertSame(30, (int) Carbon::parse($prev['from'])->diffInDays(Carbon::parse($prev['to'])) + 1);
    }
}