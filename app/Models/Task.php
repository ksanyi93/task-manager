<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lenght',
        'finished',
        'assignes',
        'priority',
        'schedule_date'
    ];

    protected $casts = [
        'lenght' => 'integer',
        'finished' => 'boolean',
        'schedule_date' => 'date'
    ];

    public function getAssignesAttribute($value): array|bool
    {
        if (empty($value)) {
            return [];
        }
        
        return array_filter(explode(',', $value));
    }

    public function setAssignesAttribute($value): void
    {
        if (is_array($value)) {
            $value = array_slice($value, 0, 4);
            $value = array_filter($value);
            $this->attributes['assignes'] = implode(',', $value);
        } else {
            $this->attributes['assignes'] = $value;
        }
    }

    public function isWeekday(): bool
    {
        return !Carbon::parse($this->schedule_date)->isWeekend();
    }
}