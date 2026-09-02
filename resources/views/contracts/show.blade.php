@extends('layouts.app')
@php
	$currentPage = 'contracts';
@endphp
@section('title', $contract->title)
@section('content')
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h1>{{ $contract->title }}</h1>
		<div>
            <a href="{{ route('contracts.index') }}" class="btn btn-secondary btn-sm me-1"> 
                <i class="fa fa-arrow-left"></i> {{__('Back to Contracts')}}
            </a>
            <a href='{{ route('contracts.edit', $contract) }}' class="btn btn-warning btn-sm">
                {{ __('Edit') }} <i class="fa fa-edit"></i>
            </a>
        </div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card shadow-sm border-light">
				<div class="card-header bg-light">
					<h5 class="text-primary mb-0"><i class="fa fa-file-text-o"></i> {{ __('Contract Details') }}</h5>
				</div>
				<div class="card-body">
                    <div class="mb-4">
                        <span class="text-muted small d-block mb-1">{{ __('Title') }}</span>
                        <h4 class="text-dark">{{ $contract->title }}</h4>
                    </div>
                    <hr>
                    
                    <div class="mt-4">
                        <span class="text-muted small d-block mb-3">{{ __('Contract Pages') }}</span>
                        @php
                            $pages = is_array($contract->pages) ? $contract->pages : [$contract->body ?? ''];
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-3 col-lg-2">
                                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    @foreach($pages as $index => $pageContent)
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }} text-start fw-bold mb-2 py-2 px-3 transition-all" 
                                            id="v-pills-page-{{ $index }}-tab" 
                                            data-bs-toggle="pill" 
                                            data-bs-target="#v-pills-page-{{ $index }}" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="v-pills-page-{{ $index }}" 
                                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            <i class="fa fa-file-text me-2"></i> {{ __('Page') }} {{ $index + 1 }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-9 col-lg-10">
                                <div class="tab-content" id="v-pills-tabContent">
                                    @foreach($pages as $index => $pageContent)
                                        <div class="tab-pane fade show {{ $index === 0 ? 'active' : '' }}" 
                                            id="v-pills-page-{{ $index }}" 
                                            role="tabpanel" 
                                            aria-labelledby="v-pills-page-{{ $index }}-tab">
                                            <div class="contract-body p-4 bg-light border rounded text-secondary" style="white-space: pre-wrap; min-height: 250px; line-height: 1.7; font-size: 1.05rem;">{{ $pageContent }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="card-footer text-muted bg-light">
                    <div class="row text-center">
                        <div class="col-md-6 small">
                            <strong>{{ __("Created At") }}:</strong> {{ $contract->created_at?->format('Y-m-d H:i') ?? '-' }}
                        </div>
                        <div class="col-md-6 small">
                            <strong>{{ __("Updated At") }}:</strong> {{ $contract->updated_at?->format('Y-m-d H:i') ?? '-' }}
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>

<style>
    .nav-pills .nav-link {
        border-radius: 6px;
        color: var(--phoenix-secondary);
        background-color: var(--phoenix-gray-100);
        border: 1px solid transparent;
    }
    .nav-pills .nav-link:hover {
        background-color: var(--phoenix-gray-200);
    }
    .nav-pills .nav-link.active {
        color: #fff;
        background-color: var(--phoenix-primary) !important;
    }
</style>
@endsection
