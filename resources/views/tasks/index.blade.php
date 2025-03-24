@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Feladatok listája</span>
                    <a href="{{ route('tasks.weekly') }}" class="btn btn-sm btn-secondary me-2">Heti nézet</a>
                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">Új feladat</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Megnevezés</th>
                                    <th>Prioritás</th>
                                    <th>Ütemezett nap</th>
                                    <th>Hossz (perc)</th>
                                    <th>Kész?</th>
                                    <th>Megbízottak</th>
                                    <th>Műveletek</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                <tr>
                                    <td>{{ $task->name }}</td>
                                    <td>
                                        @if ($task->priority === 'alacsony')
                                            <span class="badge bg-secondary">Alacsony</span>
                                        @elseif ($task->priority === 'normal')
                                            <span class="badge bg-primary">Normál</span>
                                        @elseif ($task->priority === 'magas')
                                            <span class="badge bg-danger">Magas</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->schedule_date->format('Y-m-d') }}</td>
                                    <td>{{ $task->lenght }}</td>
                                    <td>
                                        @if ($task->finished)
                                            <span class="badge bg-success">Igen</span>
                                        @else
                                            <span class="badge bg-warning">Nem</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_array($task->assignes) && count($task->assignes) > 0)
                                            @foreach($task->assignes as $assigne)
                                                <span class="badge bg-info">{{ $assigne }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Nincs megbízott</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">Szerkesztés</a>
                                            <a href="{{ route('tasks.reschedule', $task) }}" class="btn btn-sm btn-info">Átütemezés</a>
                                            <a href="{{ route('tasks.duplicate', $task) }}" class="btn btn-sm btn-success">Feladat duplikálása</a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Biztosan törölni szeretnéd?')">Törlés</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nincs még feladat rögzítve</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection