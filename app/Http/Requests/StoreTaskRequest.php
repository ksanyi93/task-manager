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
            'assignes' => 'nullable|array',
            'assignes.*' => 'string',
            'lenght' => 'nullable|integer|min:0'
        ];
    }
    
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $scheduleDate = $this->input('schedule_date');
            $assignees = $this->input('assignes') ?? [];
            $lenght = $this->input('lenght') ?? 0;
    
            foreach ($assignees as $assignee) {
                $existingMinutes = Task::where('schedule_date', $scheduleDate)
                    ->where('assignes', 'LIKE', '%' . $assignee . '%')
                    ->sum('lenght');
    
                $totalMinutes = $existingMinutes + $lenght;
    
                if ($totalMinutes > 480) {
                    $validator->errors()->add(
                        'assignes',
                        'A kiválasztott megbízott (' . $assignee . ') az adott napon (' . $scheduleDate . ') már nem tud több munkát vállalni, mert elérné a 8 órás limitet.'
                    );
                }
            }
        });
    }
}