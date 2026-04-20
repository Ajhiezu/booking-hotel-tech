@php
    $routeName = request()->route()->getName();
    $prefix = explode('.', $routeName)[0] ?? 'customer';
    
    $layout = 'layouts.app';
    if ($prefix === 'owner') $layout = 'layouts.owner';
    elseif ($prefix === 'admin') $layout = 'layouts.admin';
@endphp

@extends($layout)

@section('title', 'Profile')
@section('page-title', 'Profile Settings')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-200">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form', ['prefix' => $prefix])
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-200">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if($prefix === 'customer')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-200">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form', ['prefix' => $prefix])
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
