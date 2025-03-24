<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'schedule_date' => 'required|date',
            'assignes' => 'nullable|array', // Most már opcionális tömb
            'assignes.*' => 'string', // Minden elem string legyen
            'lenght' => 'nullable|integer|min:0' // Átneveztem duration-ról lenght-re a korábban mutatott modell alapján
        ];
    }
    
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $scheduleDate = $this->input('schedule_date');
            $assignees = $this->input('assignes') ?? []; // Alapértelmezetten üres tömb
            $lenght = $this->input('lenght') ?? 0;
    
            foreach ($assignees as $assignee) {
                // Összes létező feladat lekérdezése, ahol a string assignes mezőben benne van a felhasználó
                $existingMinutes = Task::where('schedule_date', $scheduleDate)
                    ->where('assignes', 'LIKE', '%' . $assignee . '%')
                    ->sum('lenght');
    
                // Az új feladattal együtt mennyi lesz
                $totalMinutes = $existingMinutes + $lenght;
    
                if ($totalMinutes > 480) { // 8 óra = 480 perc
                    $validator->errors()->add(
                        'assignes',
                        'A kiválasztott megbízott (' . $assignee . ') az adott napon (' . $scheduleDate . ') már nem tud több munkát vállalni, mert elérné a 8 órás limitet.'
                    );
                }
            }
        });
    }
}