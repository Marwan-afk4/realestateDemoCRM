@extends('layouts.app')
@php
	$currentPage = 'bot-messages';
@endphp
@section('title', __('Bot Messages'))
@section('content')
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-0">{{__('Bot Messages')}}</h1>
            @if($parentMessage)
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mt-2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('bot-messages.index') }}">{{ __('Root') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($parentMessage->text, 30) }}</li>
                    </ol>
                </nav>
            @else
                <p class="text-muted mb-0">{{ __('Root Level Options') }}</p>
            @endif
        </div>
		<a href="{{ route('bot-messages.create', ['parent_id' => $parentId]) }}" class="btn btn-primary btn-sm">
            {{__('Create Option')}} <i class="fa fa-plus"></i>
        </a>
	</div>
	
	<div class='main-card mb-3 card'>
		<div class='card-body'>
            @if($parentMessage)
                <div class="mb-3">
                    <a href="{{ route('bot-messages.index', ['parent_id' => $parentMessage->parent_id]) }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-level-up-alt"></i> {{ __('Go Up') }}
                    </a>
                </div>
            @endif

			<table class="mb-0 table table-hover">
				<thead>
                    <tr>
                        <th>{{ __("Id") }}</th>
                        <th>{{ __("User Option Text") }}</th>
                        <th>{{ __("Bot Response") }}</th>
                        <th class="text-center">{{ __("Sub-Options") }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                    <tr>
                        <td>{{ $message->id }}</td>
                        <td>{{ $message->text }}</td>
                        <td>{{ Str::limit($message->response, 50) ?: '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('bot-messages.index', ['parent_id' => $message->id]) }}" class="btn btn-sm btn-outline-info">
                                <i class="fa fa-folder-open"></i> {{ $message->children_count }} {{ __('Options') }}
                            </a>
                        </td>
                        <td class="text-center">
                            <a href='{{ route('bot-messages.edit', $message) }}' class="btn btn-subtle-primary btn-sm me-1" title="{{ __('Edit') }}">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('bot-messages.destroy', $message) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('{{ __('Are you sure? This will also delete ALL sub-options below this one.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-subtle-danger btn-sm" title="{{ __('Delete') }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fa fa-info-circle fa-2x mb-2"></i>
                            <p class="mb-0">{{ __('No messages found at this level.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
			</table>
		</div>
	</div>
</div>
@endsection
