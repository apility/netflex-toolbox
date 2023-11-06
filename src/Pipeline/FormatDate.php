<?php

namespace Netflex\Toolbox\Pipeline;

use Carbon\CarbonImmutable;

class FormatDate
{
    private array $field;
    private bool $date;
    private bool $time;

    private ?string $timezone = null;

    private function __construct($field, $date, $time, $timezone)
    {
        $this->field = $field;
        $this->date = $date;
        $this->time = $time;
        $this->timezone = $timezone;
    }

    /**
     *
     * Reconverts date fields to a format accepted by netflex
     *
     * @param array|string $field
     * @param bool $date
     * @param bool $time
     * @param string|null $timezone
     * @return self
     */
    public static function make($field, bool $date = true, bool $time = true, ?string $timezone = null): self
    {
        if (!is_array($field)) {
            $field = [$field];
        }
        return new static($field, $date, $time, $timezone);
    }


    public function handle(object $entry, $next)
    {

        foreach ($this->field as $key) {
            $value = $entry->{$key};

            if ($value) {
                $time = CarbonImmutable::parse($value, $this->timezone);

                if ($this->timezone) {
                    $time = $time->setTimezone($this->timezone);
                }

                if ($time) {
                    $entry->{$key} = $time->format($this->getTimeFormat());
                }
            }
        }

        return $next($entry);
    }

    private function getTimeFormat(): string
    {
        return collect([
            $this->date ? 'Y-m-d' : null,
            $this->time ? 'H:i:s' : null,
        ])->values()->join(" ");
    }
}
