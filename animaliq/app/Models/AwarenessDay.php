<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AwarenessDay extends Model
{
    protected $fillable = ['title', 'celebration_date', 'body', 'image', 'active'];

    /** Timezone used for "today" / celebration matching (Kenya). */
    public static function celebrationTimezone(): string
    {
        return config('app.awareness_timezone', 'Africa/Nairobi');
    }

    public static function celebrationToday(): Carbon
    {
        return Carbon::now(static::celebrationTimezone())->startOfDay();
    }

    protected function casts(): array
    {
        return [
            'celebration_date' => 'date',
            'active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Match by month + day every year (awareness days are annual, not year-specific).
     */
    public function scopeToday(Builder $query): Builder
    {
        $today = static::celebrationToday();

        return $query
            ->whereMonth('celebration_date', $today->month)
            ->whereDay('celebration_date', $today->day);
    }

    /**
     * Remaining celebrations this calendar year (by month/day), ordered chronologically.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        $today = static::celebrationToday();

        return $query
            ->where(function (Builder $q) use ($today) {
                $q->whereMonth('celebration_date', '>', $today->month)
                    ->orWhere(function (Builder $q2) use ($today) {
                        $q2->whereMonth('celebration_date', $today->month)
                            ->whereDay('celebration_date', '>=', $today->day);
                    });
            })
            ->orderByRaw('MONTH(celebration_date) ASC, DAY(celebration_date) ASC');
    }

    /** Whether this day is celebrated today (annual month/day match). */
    public function isCelebratingToday(): bool
    {
        $today = static::celebrationToday();
        $date = $this->celebration_date;

        return $date
            && (int) $date->month === (int) $today->month
            && (int) $date->day === (int) $today->day;
    }

    /** True if this year's occurrence has already passed (and it is not today). */
    public function isPastThisYear(): bool
    {
        if ($this->isCelebratingToday()) {
            return false;
        }

        $today = static::celebrationToday();
        $date = $this->celebration_date;
        if (! $date) {
            return false;
        }

        if ((int) $date->month < (int) $today->month) {
            return true;
        }

        return (int) $date->month === (int) $today->month
            && (int) $date->day < (int) $today->day;
    }

    /** Next occurrence of this day (this year or next), in celebration timezone. */
    public function nextOccurrence(): Carbon
    {
        $today = static::celebrationToday();
        $date = $this->celebration_date;
        $occurrence = Carbon::create(
            $today->year,
            (int) $date->month,
            (int) $date->day,
            0,
            0,
            0,
            static::celebrationTimezone()
        )->startOfDay();

        if ($occurrence->lt($today)) {
            $occurrence->addYear();
        }

        return $occurrence;
    }

    public function daysUntilNext(): int
    {
        return (int) static::celebrationToday()->diffInDays($this->nextOccurrence(), false);
    }
}
