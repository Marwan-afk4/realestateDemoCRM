{{ __('Hello :name,', ['name' => $user->full_name]) }}

{{ __('Thank you for agreeing to this contract. A full copy is included below for your records.') }}

{{ __('Contract Title') }}: {{ $contract->title }}
{{ __('Agreement reference') }}: #{{ $agreement->id }}
{{ __('Agreement date') }}: {{ $agreedAt->format('Y-m-d H:i') }}
{{ __('Name') }}: {{ $user->full_name }}

{{ __('Contract Details') }}
@foreach($pages as $index => $pageContent)

{{ __('Page :number', ['number' => $index + 1]) }}
{{ str_repeat('-', 32) }}
{{ $pageContent }}
@endforeach

{{ __('This is an official copy of the agreement recorded in your account.') }}
{{ __('Please keep this email for your records.') }}

--
{{ $appName }}
