@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Crew Category') }}
                    <br>
                    <ul>
                        @foreach ($crewCategories as $category)
                            <li>{{ $category->title }}</li>
                        @endforeach
                    </ul>

                    {{ $crewCategories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
