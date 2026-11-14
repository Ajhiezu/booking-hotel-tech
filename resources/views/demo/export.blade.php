@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.owner')
@section('title', 'Demo Export')
@section('page-title', 'Demo Export')
@section('content')
    <div class="bg-white border border-gray-100 rounded-2xl p-8">
        <h1 class="text-xl font-bold mb-3">Report Export Preview</h1>
        <p class="text-gray-600 mb-5">Reports use sample data in demo mode. File downloads are available when demo mode is disabled and a database is connected.</p>
        <a class="text-blue-600 underline" href="{{ route(auth()->user()->isSuperAdmin() ? 'admin.reports.index' : 'owner.reports.index') }}">Back to Reports</a>
    </div>
@endsection
