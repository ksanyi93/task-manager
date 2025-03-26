@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Feladatok heti nézete: {{ $currentDate->format('Y. F') }}</span>
                    <div>
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary me-2">Vissza a listához</a>
                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">Új feladat</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Navigációs gombok -->
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('tasks.weekly.date', $prevWeek) }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Előző hét
                        </a>
                        <a href="{{ route('tasks.weekly.date', $nextWeek) }}" class="btn btn-outline-primary">
                            Következő hét <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Heti nézet -->
                    <div class="row">
                        @foreach ($weekDays as $day)
                            @php
                                $dateString = $day->format('Y-m-d');
                                $isToday = $day->isToday();
                            @endphp
                            <div class="col">
                                <div class="card {{ $isToday ? 'border-primary' : '' }}">
                                    <div class="card-header {{ $isToday ? 'bg-primary text-white' : 'bg-light' }}">
                                        <strong>{{ $day->format('l') }}</strong>
                                        <br>
                                        <small>{{ $day->format('Y-m-d') }}</small>
                                    </div>
                                    <div class="card-body" style="min-height: 300px;">
                                        @if (isset($tasks[$dateString]) && $tasks[$dateString]->count() > 0)
                                            @foreach ($tasks[$dateString] as $task)
                                                <div class="task-card mb-2 p-2 border rounded 
                                                    @if ($task->priority === 'alacsony') border-secondary
                                                    @elseif ($task->priority === 'normal') border-primary
                                                    @elseif ($task->priority === 'magas') border-danger
                                                    @endif">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>{{ $task->name }}</strong>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center"><small>Nincs ütemezett feladat</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ resource_path('css/weeklyColor.css') }}">

@endsection