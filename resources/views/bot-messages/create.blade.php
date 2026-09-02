@extends('layouts.app')
@php
	$currentPage = 'bot-messages';
@endphp
@section('title', __('Create Bot Message'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Create Bot Message')}}</h1>
	<div class="mb-3">
		<a href="{{ route('bot-messages.index', ['parent_id' => $parentId]) }}" class="btn btn-secondary btn-sm"> 
            <i class="fa fa-arrow-left"></i> {{__('Back')}}
        </a>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="card">
				<div class="card-body">
					<form action="{{ route('bot-messages.store') }}" method="POST">
						@csrf
						
                        <div class="mb-3">
                            <label class="form-label">{{ __('Parent Option') }}</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">{{ __('Root Level (No Parent)') }}</option>
                                @foreach($allMessages as $msg)
                                    <option value="{{ $msg->id }}" {{ old('parent_id', $parentId) == $msg->id ? 'selected' : '' }}>
                                        {{ $msg->text }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Select which option the user must click to see this new option.') }}</div>
                            @error('parent_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

						<div class="mb-3">
                            <label class="form-label">{{ __('User Option Text') }} <span class="text-danger">*</span></label>
                            <input type="text" name="text" class="form-control @error('text') is-invalid @enderror" value="{{ old('text') }}" required>
                            <div class="form-text">{{ __('The text the user will see and click on (e.g., "I need help with my account").') }}</div>
                            @error('text') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Bot Response (Optional)') }}</label>
                            <textarea name="response" class="form-control @error('response') is-invalid @enderror" rows="4">{{ old('response') }}</textarea>
                            <div class="form-text">{{ __('The text the bot replies with after the user clicks the option above. Leave blank if the bot should just show the next sub-options.') }}</div>
                            @error('response') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

						<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
