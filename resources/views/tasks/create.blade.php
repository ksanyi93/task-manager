@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Új feladat létrehozása</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Megnevezés <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="schedule_date" class="form-label">Ütemezett nap <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('schedule_date') is-invalid @enderror" id="schedule_date" name="schedule_date" value="{{ old('schedule_date') }}" required>
                            <small class="form-text text-muted">Csak hétköznap (H-P) adható meg.</small>
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioritás</label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                <option value="alacsony" {{ old('priority') == 'alacsony' ? 'selected' : '' }}>Alacsony</option>
                                <option value="normal" {{ old('priority') == 'normal' || old('priority') === null ? 'selected' : '' }}>Normál</option>
                                <option value="magas" {{ old('priority') == 'magas' ? 'selected' : '' }}>Magas</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lenght" class="form-label">Hossz (percben)</label>
                            <input type="number" class="form-control @error('lenght') is-invalid @enderror" id="lenght" name="lenght" value="{{ old('lenght', 0) }}" min="0">
                            @error('lenght')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('finished') is-invalid @enderror" id="finished" name="finished" value="1" {{ old('finished') ? 'checked' : '' }}>
                            <label class="form-check-label" for="finished">Kész?</label>
                            @error('finished')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Megbízottak (max. 4 fő)</label>
                            <div class="assignes-container">
                                @for ($i = 0; $i < 4; $i++)
                                    <div class="mb-2">
                                        <input type="text" class="form-control @error('assignes.'.$i) is-invalid @enderror" name="assignes[]" value="{{ old('assignes.'.$i) }}" placeholder="{{ $i+1 }}. megbízott">
                                        @error('assignes.'.$i)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endfor
                            </div>
                            @error('assignes')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Mentés</button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Vissza</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.modals.error-modal')

@if ($errors->any())
    <script src="{{ asset('js/errorModal.js') }}"></script>
@endif

<script src="{{ asset('js/weekdayValidation.js') }}"></script>

@endsection