@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem;">🕵️ Phishing Pages</h1>
                <p style="color:#718096;">Create and manage phishing landing pages for your campaigns</p>
            </div>
            <a href="{{ route('client.phishing-pages.create') }}" class="btn btn-primary">+ Create Phishing Page</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($phishingPages->count() === 0)
            <div style="text-align: center; padding: 3rem; color: #718096;">
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">No phishing pages found.</p>
                <a href="{{ route('client.phishing-pages.create') }}" class="btn btn-primary">Create Your First Phishing Page</a>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;">
                @foreach($phishingPages as $page)
                    <div class="card" style="padding:1.5rem; display: flex; flex-direction: column; height: 100%;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <h3 style="margin:0; font-size: 1.25rem; font-weight: 600;">{{ $page->name }}</h3>
                                @if($page->is_active)
                                    <span style="padding: 0.25rem 0.5rem; background-color: #c6f6d5; color: #22543d; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                        Active
                                    </span>
                                @else
                                    <span style="padding: 0.25rem 0.5rem; background-color: #e2e8f0; color: #4a5568; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            <p style="color:#718096; margin-bottom: 1rem; text-transform: capitalize; font-size: 0.875rem;">
                                <span style="padding: 0.25rem 0.75rem; background-color: {{ $page->type === 'phishing' ? '#fed7d7' : ($page->type === 'awareness' ? '#bee3f8' : '#c6f6d5') }}; color: {{ $page->type === 'phishing' ? '#c53030' : ($page->type === 'awareness' ? '#2c5282' : '#22543d') }}; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                    {{ $page->type }}
                                </span>
                            </p>
                            @if($page->description)
                                <p style="color:#718096; font-size: 0.875rem; margin-bottom: 1rem;">
                                    {{ $page->description }}
                                </p>
                            @endif
                            @if($page->slug)
                                <p style="color:#2b6cb0; font-size: 0.75rem; margin-bottom: 0.5rem;">
                                    🔗 <a href="{{ route('public.phishing-page', $page->slug) }}" target="_blank" style="color: #2b6cb0; text-decoration: underline;">
                                        {{ config('app.url') }}/page/{{ $page->slug }}
                                    </a>
                                </p>
                            @endif
                            <p style="color:#718096; font-size: 0.75rem; margin-bottom: 1rem;">
                                Created: {{ $page->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div style="display:flex;gap:.5rem; margin-top: auto; flex-wrap: wrap;">
                            <a href="{{ route('client.phishing-pages.preview', $page->id) }}" class="btn btn-secondary" target="_blank" style="flex: 1; text-align: center;">
                                👁️ Preview
                            </a>
                            <a href="{{ route('client.phishing-pages.edit', $page->id) }}" class="btn btn-secondary" style="flex: 1; text-align: center;">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('client.phishing-pages.delete', $page->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to delete this phishing page?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="width: 100%; background-color: #fc8181; color: white;">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:2rem;">
                {{ $phishingPages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

