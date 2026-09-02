@extends('layouts.app')
@php
	$currentPage = 'unit-sub-types';
@endphp
@section('title', __('Edit Unit Sub-Type'))
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-3">{{__('Edit Unit Sub-Type')}}</h1>
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <form action="{{ route('unit-sub-types.update', $unitSubType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="uptown_type_id">{{ __('Parent Unit Type') }}</label>
                            <select name="uptown_type_id" id="uptown_type_id" class="form-control @error('uptown_type_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select Unit Type') }}</option>
                                @foreach($uptownTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('uptown_type_id', $unitSubType->uptown_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('uptown_type_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="name_en">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $unitSubType->name_en) }}" required>
                            @error('name_en')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="name_ar">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $unitSubType->name_ar) }}">
                            @error('name_ar')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('unit-sub-types.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
