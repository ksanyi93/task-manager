<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('schedule_date', 'asc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    private function getValidationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'lenght' => 'nullable|integer|min:0',
            'finished' => 'nullable|boolean',
            'assignes' => 'nullable|array|max:4',
            'assignes.*' => 'nullable|string',
            'priority' => 'nullable|in:alacsony,normal,magas',
            'schedule_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Az ütemezett nap csak hétköznap lehet (hétfő-péntek).');
                    }
                },
                $this->createWorkValidation()
            ]
        ];
    }

    private function createWorkValidation()
    {
        return function ($attribute, $value, $fail) {
            $request = request();
            
            if (!$request->has('assignes') || empty($request->input('assignes'))) {
                return;
            }
            
            $newTaskLength = $request->input('lenght') ?? 0;
            
            foreach ($request->input('assignes') as $assignee) {
                $existingMinutes = Task::where('schedule_date', $value)
                    ->where('assignes', 'LIKE', '%' . $assignee . '%')
                    ->sum('lenght');
                
                $totalMinutes = $existingMinutes + $newTaskLength;
                
                if ($totalMinutes > 480 && !empty($assignee)) {
                    $fail("A(z) $assignee megbízott az adott napon már nem tud több munkát vállalni (8 óra / 480 perc feletti munkaidő).");
                }
            }
        };
    }

    private function normalizeValidatedData(array $validated): array
    {
        $normalized = array_merge([
            'lenght' => 0,
            'finished' => false,
            'assignes' => '',
            'priority' => 'normal'
        ], $validated);

        if (is_array($normalized['assignes'])) {
            $normalized['assignes'] = implode(',', $normalized['assignes']);
        }

        return $normalized;
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate($this->getValidationRules());
        $normalizedData = $this->normalizeValidatedData($validated);
        
        $task->update($normalizedData);
        
        return redirect()->route('tasks.index')
            ->with('success', 'Feladat sikeresen frissítve!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());
        $normalizedData = $this->normalizeValidatedData($validated);
        
        Task::create($normalizedData);
        
        return redirect()->route('tasks.index')
            ->with('success', 'Feladat sikeresen létrehozva!');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Feladat sikeresen törölve!');
    }

    public function reschedule(Task $task)
    {
        return view('tasks.reschedule', compact('task'));
    }

    public function updateSchedule(Request $request, Task $task)
    {
        $request->validate([
            'schedule_date' => 'required|date',
        ]);

        $dayOfWeek = date('N', strtotime($request->schedule_date));
        if ($dayOfWeek >= 6) {
            return back()
                ->withInput()
                ->withErrors(['schedule_date' => 'Csak hétköznap (hétfő-péntek) választható!']);
        }

        $task->schedule_date = $request->schedule_date;
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'A feladat sikeresen átütemezve!');
    }

    public function duplicate(Task $task)
    {
        $workloadValidationClosure = $this->createWorkValidation();

        $request = request()->replace([
            'schedule_date' => $task->schedule_date,
            'assignes' => $task->assignes,
            'lenght' => $task->lenght
        ]);

        try {
            $workloadValidationClosure('schedule_date', $task->schedule_date, function($message) {
                throw new \Exception($message);
            });
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')
                ->with('error', $e->getMessage());
        }

        $newTask = $task->replicate();
        $newTask->name = $task->name . ' (másolat)';
        $newTask->save();
    
        return redirect()->route('tasks.index')
            ->with('success', 'A feladat sikeresen duplikálva!');
    }

    public function weekly($date = null)
    {
        $weekDays = [];
        $currentDate = !is_null($date) ? Carbon::parse($date) : Carbon::now();
        $weekStart = $currentDate->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->addDays(4);

        for ($i = 0; $i < 5; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weekDays[] = $day;
        }
        
        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        
        $tasks = Task::whereDate('schedule_date', '>=', $weekStart->format('Y-m-d'))
                    ->whereDate('schedule_date', '<=', $weekEnd->format('Y-m-d'))
                    ->orderBy('priority', 'desc')
                    ->get()
                    ->groupBy(function($task) {
                        return $task->schedule_date->format('Y-m-d');
                    });
        
        return view('tasks.weekly', compact('weekDays', 'tasks', 'prevWeek', 'nextWeek', 'currentDate'));
    }
}