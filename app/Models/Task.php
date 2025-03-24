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

    // Setter for 'assignes', limit to 4 assigned people
    public function setAssignesAttribute($value): void
    {
        // Ha tömböt kap, alakítsa át stringgé
        if (is_array($value)) {
            // Maximálja 4 főre
            $value = array_slice($value, 0, 4);
            
            // Távolítsa el az üres elemeket
            $value = array_filter($value);
            
            // Alakítsa át stringgé vesszővel elválasztva
            $this->attributes['assignes'] = implode(',', $value);
        } else {
            // Ha nem tömb, egyszerűen mentse úgy ahogy van
            $this->attributes['assignes'] = $value;
        }
    }

    // Validate if scheduled date is a weekday
    public function isWeekday(): bool
    {
        return !Carbon::parse($this->schedule_date)->isWeekend();
    }
}