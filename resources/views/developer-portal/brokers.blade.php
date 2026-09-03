@extends('layouts.app')
@php $currentPage = 'developer-portal'; @endphp
@section('title', __('Authorized brokers'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <h2 class="mb-4">{{ __('Authorized brokers') }}</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('developer-portal.brokers.sync') }}">
                @csrf
                <p class="text-body-secondary">{{ __('Select brokers allowed to sell this developer\'s inventory.') }}</p>
                @foreach($brokers as $id => $name)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="brocker_ids[]" value="{{ $id }}" id="broker-{{ $id }}" @checked(in_array($id, $authorized))>
                        <label class="form-check-label" for="broker-{{ $id }}">{{ $name }}</label>
                    </div>
                @endforeach
                <button class="btn btn-primary mt-3" type="submit">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
