@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Feladat átütemezése</span>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary">Vissza a listához</a>
                </div>

                <div class="card-body">
                    <h5 class="mb-3">{{ $task->name }}</h5>
                    
                    <div class="mb-3">
                        <p><strong>Jelenlegi ütemezett dátum:</strong> {{ $task->schedule_date->format('Y-m-d') }}</p>
                    </div>

                    <form action="{{ route('tasks.update_schedule', $task->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label for="schedule_date" class="form-label">Új dátum kiválasztása</label>
                            <input 
                                type="date" 
                                class="form-control @error('schedule_date') is-invalid @enderror" 
                                id="schedule_date" 
                                name="schedule_date" 
                                value="{{ old('schedule_date', $task->schedule_date->format('Y-m-d')) }}"
                            >
                            @error('schedule_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Átütemezés mentése</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/weekdayValidation.js') }}"></script>

@endsection