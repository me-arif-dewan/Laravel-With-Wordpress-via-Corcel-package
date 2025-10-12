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

                    {{ __('Posts') }}
                    <br>
                    <ul>
                        @foreach ($posts as $post)
                            <li>{{ $post->post_title }}</li>
                            <?php $post->acf; ?>
                            <?php $taxonomy = $post->taxonomies()->first();
                                echo $taxonomy->taxonomy ?>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
