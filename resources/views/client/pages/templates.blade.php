@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem;">Email Templates</h1>
                <p style="color:#718096;">Browse and preview available email templates for your campaigns</p>
            </div>
            <a href="{{ route('client.campaigns.create') }}" class="btn btn-primary">+ Create Campaign</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($templates->count() === 0)
            <div style="text-align: center; padding: 3rem; color: #718096;">
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">No templates found.</p>
                <a href="{{ route('client.campaigns.create') }}" class="btn btn-primary">Create Your First Campaign</a>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;">
                @foreach($templates as $template)
                    <div class="card" style="padding:1.5rem; display: flex; flex-direction: column; height: 100%;">
                        <div style="flex: 1;">
                            <h3 style="margin:0 0 .5rem 0; font-size: 1.25rem; font-weight: 600;">{{ $template->name }}</h3>
                            <p style="color:#718096; margin-bottom: 1rem; text-transform: capitalize; font-size: 0.875rem;">
                                <span style="padding: 0.25rem 0.75rem; background-color: {{ $template->type === 'phishing' ? '#fed7d7' : ($template->type === 'awareness' ? '#bee3f8' : '#c6f6d5') }}; color: {{ $template->type === 'phishing' ? '#c53030' : ($template->type === 'awareness' ? '#2c5282' : '#22543d') }}; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                    {{ $template->type }}
                                </span>
                            </p>
                            <p style="color:#718096; font-size: 0.875rem; margin-bottom: 1rem;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($template->html_content), 100) }}
                            </p>
                        </div>
                        <div style="display:flex;gap:.5rem; margin-top: auto;">
                            <a href="{{ route('client.templates.use', $template->id) }}" class="btn btn-primary" style="flex: 1; text-align: center;">
                                ✨ Use Template
                            </a>
                            <a href="{{ route('client.templates.preview', $template->id) }}" class="btn btn-secondary" target="_blank">
                                👁️ Preview
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:2rem;">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection



