@extends('layouts.app')
@php
	$currentPage = 'contracts';
@endphp
@section('title', __('Edit Contract'))
@section('content')
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h1>{{ __('Edit Contract') }}</h1>
		<a href="{{ route('contracts.index') }}" class="btn btn-secondary btn-sm"> 
            <i class="fa fa-arrow-left"></i> {{__('Back to Contracts')}}
        </a>
	</div>

	<div class="main-card mb-3 card shadow-sm">
		<div class="card-body">
			<form method='POST' action='{{ route('contracts.update', $contract) }}' class='needs-validation' novalidate>
				@csrf
                @method('PUT')

				<div class="row mb-4">
					<div class="col-12">
						<h5 class="text-primary">{{ __('Contract Information') }}</h5>
						<hr>
					</div>
				</div>

				<x-form-input
					name="title"
					type="text"
					label="{{__('Contract Title')}}"
                    :value="$contract->title"
					required
				/>

                <!-- Contract Pages Builder -->
                <div class="mt-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-primary mb-0"><i class="fa fa-file-text-o"></i> {{ __('Contract Pages') }}</h5>
                        <button type="button" id="add-page-btn" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> {{ __('Add Page') }}
                        </button>
                    </div>

                    <div id="pages-container">
                        @php
                            $pages = is_array($contract->pages) ? $contract->pages : [$contract->body ?? ''];
                        @endphp
                        @foreach($pages as $index => $pageContent)
                        <!-- Page Card -->
                        <div class="card mb-3 page-card border-light shadow-xs transition-all">
                            <div class="card-header bg-subtle-secondary d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold text-secondary page-number">{{ __('Page') }} {{ $index + 1 }}</span>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-xs move-up-btn" title="{{ __('Move Up') }}">
                                        <i class="fa fa-chevron-up"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-xs move-down-btn" title="{{ __('Move Down') }}">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs delete-page-btn" title="{{ __('Delete Page') }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="form-floating">
                                    <textarea
                                        name="pages[{{ $index }}]"
                                        class="form-control"
                                        placeholder="{{ __('Page Content') }}..."
                                        style="min-height: 200px"
                                        required
                                    >{{ $pageContent }}</textarea>
                                    <label>{{ __('Page Content') }}</label>
                                </div>
                                <div class="invalid-feedback">
                                    {{ __('Please enter content for this page.') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Update Contract') }}</button>
			</form>
		</div>
	</div>
</div>

<style>
    .page-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .page-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('pages-container');
    const addButton = document.getElementById('add-page-btn');

    function updatePageIndexes() {
        const cards = container.querySelectorAll('.page-card');
        cards.forEach((card, index) => {
            const pageNum = index + 1;
            // Update title
            card.querySelector('.page-number').textContent = `{{ __('Page') }} ${pageNum}`;
            // Update textarea name/id
            const textarea = card.querySelector('textarea');
            textarea.name = `pages[${index}]`;
            textarea.id = `page_${pageNum}`;
            
            // Toggle delete button: disable/hide if only 1 page
            const deleteBtn = card.querySelector('.delete-page-btn');
            if (cards.length === 1) {
                deleteBtn.style.display = 'none';
            } else {
                deleteBtn.style.display = 'inline-block';
            }

            // Reordering buttons visibility / disabled states
            const upBtn = card.querySelector('.move-up-btn');
            const downBtn = card.querySelector('.move-down-btn');
            
            upBtn.disabled = (index === 0);
            downBtn.disabled = (index === cards.length - 1);
        });
    }

    addButton.addEventListener('click', function () {
        const cards = container.querySelectorAll('.page-card');
        let template;
        if (cards.length > 0) {
            template = cards[0].cloneNode(true);
        } else {
            // Re-create from dummy block if somehow empty
            const div = document.createElement('div');
            div.innerHTML = `
                <div class="card mb-3 page-card border-light shadow-xs transition-all">
                    <div class="card-header bg-subtle-secondary d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold text-secondary page-number">{{ __('Page 1') }}</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary btn-xs move-up-btn" title="{{ __('Move Up') }}"><i class="fa fa-chevron-up"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-xs move-down-btn" title="{{ __('Move Down') }}"><i class="fa fa-chevron-down"></i></button>
                            <button type="button" class="btn btn-danger btn-xs delete-page-btn" title="{{ __('Delete Page') }}"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-floating">
                            <textarea name="pages[0]" class="form-control" placeholder="{{ __('Page Content') }}..." style="min-height: 200px" required></textarea>
                            <label>{{ __('Page Content') }}</label>
                        </div>
                        <div class="invalid-feedback">{{ __('Please enter content for this page.') }}</div>
                    </div>
                </div>`;
            template = div.firstElementChild;
        }
        // Reset textarea value
        template.querySelector('textarea').value = '';
        container.appendChild(template);
        updatePageIndexes();
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.delete-page-btn')) {
            const card = e.target.closest('.page-card');
            card.remove();
            updatePageIndexes();
        } else if (e.target.closest('.move-up-btn')) {
            const card = e.target.closest('.page-card');
            const prev = card.previousElementSibling;
            if (prev) {
                container.insertBefore(card, prev);
                updatePageIndexes();
            }
        } else if (e.target.closest('.move-down-btn')) {
            const card = e.target.closest('.page-card');
            const next = card.nextElementSibling;
            if (next) {
                container.insertBefore(next, card);
                updatePageIndexes();
            }
        }
    });

    // Run once on load to initialize
    updatePageIndexes();
});
</script>
@endsection
