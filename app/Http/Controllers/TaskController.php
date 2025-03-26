<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
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
                function ($attribute, $value, $fail) use ($request) {
                    // Ha nincsenek megbízottak, nem kell ellenőrizni
                    if (!$request->has('assignes') || empty($request->input('assignes'))) {
                        return;
                    }

                    // Az új feladat hossza
                    $newTaskLength = $request->input('lenght') ?? 0;

                    // Ellenőrizzük minden egyes megbízottra
                    foreach ($request->input('assignes') as $assignee) {
                        // Összeszámoljuk az adott napon már meglévő feladatok hosszát
                        $existingMinutes = Task::where('schedule_date', $value)
                            ->where('assignes', 'LIKE', '%' . $assignee . '%')
                            ->sum('lenght');

                        // Teljes munkaidő az új feladattal együtt
                        $totalMinutes = $existingMinutes + $newTaskLength;

                        // 8 óra (480 perc) feletti munkaidő esetén hiba
                        if ($totalMinutes > 480) {
                            $fail("A(z) $assignee megbízott az adott napon már nem tud több munkát vállalni (8 óra / 480 perc feletti munkaidő).");
                        }
                    }
                }
            ]
        ]);

        // Alapértelmezett értékek beállítása, ha nincsenek megadva
        if (!isset($validated['lenght'])) {
            $validated['lenght'] = 0;
        }
        if (!isset($validated['finished'])) {
            $validated['finished'] = false;
        }
        if (!isset($validated['assignes'])) {
            $validated['assignes'] = '';
        } else {
            // Alakítsd át a tömböt vesszővel elválasztott stringgé
            $validated['assignes'] = implode(',', $validated['assignes']);
        }
        if (!isset($validated['priority'])) {
            $validated['priority'] = 'normal';
        }

        Task::create($validated);
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

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
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
                function ($attribute, $value, $fail) use ($request) {
                    // Ha nincsenek megbízottak, nem kell ellenőrizni
                    if (!$request->has('assignes') || empty($request->input('assignes'))) {
                        return;
                    }

                    // Az új feladat hossza
                    $newTaskLength = $request->input('lenght') ?? 0;

                    // Ellenőrizzük minden egyes megbízottra
                    foreach ($request->input('assignes') as $assignee) {
                        // Összeszámoljuk az adott napon már meglévő feladatok hosszát
                        $existingMinutes = Task::where('schedule_date', $value)
                            ->where('assignes', 'LIKE', '%' . $assignee . '%')
                            ->sum('lenght');

                        // Teljes munkaidő az új feladattal együtt
                        $totalMinutes = $existingMinutes + $newTaskLength;

                        // 8 óra (480 perc) feletti munkaidő esetén hiba
                        if ($totalMinutes > 480) {
                            $fail("A(z) $assignee megbízott az adott napon már nem tud több munkát vállalni (8 óra / 480 perc feletti munkaidő).");
                        }
                    }
                }
            ]
        ]);

        // Set default values if fields are not provided
        if (!isset($validated['lenght'])) {
            $validated['lenght'] = 0;
        }
        if (!isset($validated['finished'])) {
            $validated['finished'] = false;
        }
        if (!isset($validated['assignes'])) {
            $validated['assignes'] = '';
        } else {
            // Alakítsd át a tömböt vesszővel elválasztott stringgé
            $validated['assignes'] = implode(',', $validated['assignes']);
        }
        if (!isset($validated['priority'])) {
            $validated['priority'] = 'normal';
        }

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Feladat sikeresen frissítve!');
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

        // Ellenőrizzük, hogy a kiválasztott dátum hétköznap-e
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
        // Ellenőrizzük a munkaidő-korlátot
        $scheduleDate = $task->schedule_date;
        $assignees = $task->assignes; // Ez már tömböt ad vissza a getter miatt
        $newTaskLength = $task->lenght;
    
        // Ha vannak megbízottak, ellenőrizzük mindegyikre
        if (!empty($assignees)) {
            foreach ($assignees as $assignee) {
                // Összeszámoljuk az adott napon már meglévő feladatok hosszát
                $existingMinutes = Task::where('schedule_date', $scheduleDate)
                    ->where('assignes', 'LIKE', '%' . $assignee . '%')
                    ->sum('lenght');
    
                // Teljes munkaidő az új feladattal együtt
                $totalMinutes = $existingMinutes + $newTaskLength;
    
                // 8 óra (480 perc) feletti munkaidő esetén hiba
                if ($totalMinutes > 480) {
                    //$assignees = implode(',', $assignees);
                    return redirect()->route('tasks.index')
                        ->with('error', "A(z) $assignee megbízott az adott napon már nem tud több munkát vállalni (8 óra / 480 perc feletti munkaidő).");
                }
            }
        }
    
        // Ha minden rendben, duplikáljuk a feladatot
        $newTask = $task->replicate();
        $newTask->name = $task->name . ' (másolat)';
        $newTask->save();
       
        return redirect()->route('tasks.index')
            ->with('success', 'A feladat sikeresen duplikálva!');
    }

    public function weekly($date = null)
    {
        // Ha nincs dátum megadva, akkor a mai napot használjuk
        $currentDate = $date ? Carbon::parse($date) : Carbon::now();
        
        // A hét első napja (hétfő)
        $weekStart = $currentDate->copy()->startOfWeek();
        
        // A hét utolsó napja (péntek)
        $weekEnd = $weekStart->copy()->addDays(4);
        
        // Hétfőtől-péntekig dátumok létrehozása
        $weekDays = [];
        for ($i = 0; $i < 5; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weekDays[] = $day;
        }
        
        // Előző és következő hét dátumai a navigációhoz
        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        
        // Feladatok lekérése az aktuális hétre (hétfőtől-péntekig)
        $tasks = Task::whereDate('schedule_date', '>=', $weekStart->format('Y-m-d'))
                    ->whereDate('schedule_date', '<=', $weekEnd->format('Y-m-d'))
                    ->orderBy('priority', 'desc') // Prioritás szerinti rendezés
                    ->get()
                    ->groupBy(function($task) {
                        return $task->schedule_date->format('Y-m-d');
                    });
        
        return view('tasks.weekly', compact('weekDays', 'tasks', 'prevWeek', 'nextWeek', 'currentDate'));
    }
}