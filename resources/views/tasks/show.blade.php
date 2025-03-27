@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Feladat átütemezése</span>
                    <div>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">Teljes szerkesztés</a>
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary">Vissza</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Megnevezés:</div>
                        <div class="col-md-8">{{ $task->name }}</div>
                    </div>

                    <form action="{{ route('tasks.update.schedule', $task->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">
                                <label for="schedule_date">Ütemezett nap:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="date" id="schedule_date" name="schedule_date" 
                                    class="form-control @error('schedule_date') is-invalid @enderror" 
                                    value="{{ old('schedule_date', $task->schedule_date->format('Y-m-d')) }}" required>
                                <small class="form-text text-muted">Csak hétköznap (H-P) adható meg.</small>
                                @error('schedule_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Átütemezés mentése</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Prioritás:</div>
                        <div class="col-md-8">
                            @if ($task->prioritas === 'alacsony')
                                <span class="badge bg-secondary">Alacsony</span>
                            @elseif ($task->prioritas === 'normal')
                                <span class="badge bg-primary">Normál</span>
                            @elseif ($task->prioritas === 'magas')
                                <span class="badge bg-danger">Magas</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Hossz:</div>
                        <div class="col-md-8">{{ $task->hossz }} perc</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Állapot:</div>
                        <div class="col-md-8">
                            @if ($task->kesz)
                                <span class="badge bg-success">Kész</span>
                            @else
                                <span class="badge bg-warning">Folyamatban</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Megbízottak:</div>
                        <div class="col-md-8">
                            @if (is_array($task->megbizottak) && count($task->megbizottak) > 0)
                                @foreach($task->megbizottak as $megbizott)
                                    <span class="badge bg-info me-1">{{ $megbizott }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Nincs megbízott</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/weekdayValidation.js') }}"></script>

@endsection