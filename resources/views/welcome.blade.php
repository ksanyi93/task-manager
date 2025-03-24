@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="my-4">Üdvözöllek a feladatkezelő projekten!</h1>
    <div class="d-flex justify-content-center">
        <a href="{{ route('tasks.index') }}" class="btn btn-primary mx-2">Irány a feladatkezelő</a>
    </div>
</div>
@endsection