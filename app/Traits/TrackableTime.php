<?php

namespace App\Traits;

trait TrackableTime
{
    public function formatTime($decimalHours): string
    {
        $decimalHours = (float) $decimalHours;
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);

        return sprintf('%dh%02d', $hours, $minutes);
    }
    public function getCompteurTempsAttribute()
    {
        $diff = $this->estimated_hours - $this->actual_hours;

        if ($diff == 0)
            return "OK";

        $prefix = $diff > 0 ? "Gain : " : "Perte : ";
        return $prefix . $this->formatTime(abs($diff));
    }

    public static function convertToHours($h, $m): float
    {
        return (float) ($h ?? 0) + ((float) ($m ?? 0) / 60);
    }

    public function getDisplayEstimatedAttribute(): string
    {
        return $this->formatTime($this->estimated_hours);
    }

    public function getDisplayActualAttribute(): string
    {
        return $this->formatTime($this->actual_hours);
    }

    public function getStatusCellClassAttribute(): string
    {
        return 'cell-' . \Illuminate\Support\Str::slug($this->status);
    }

    public function getStatusTextClassAttribute() : string {
        return 'text-' . \Illuminate\Support\Str::slug($this->status);
    }

    public function getIsUrgentAttribute(): bool
    {
        return \Carbon\Carbon::parse($this->due_date)->lte(now()->addDays(7));
    }
}