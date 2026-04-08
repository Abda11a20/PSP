@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">✏️ Edit Phishing Page</h1>
        <p style="color: #718096; margin-bottom: 1.5rem;">Update your phishing landing page.</p>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('client.phishing-pages.update', $phishingPage->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Name <span style="color: red;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $phishingPage->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $phishingPage->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">URL Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $phishingPage->slug) }}" placeholder="Auto-generated from name">
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Public URL: <a href="{{ route('public.phishing-page', $phishingPage->slug) }}" target="_blank" style="color: #2b6cb0;">{{ config('app.url') }}/page/{{ $phishingPage->slug }}</a>
                </small>
            </div>

            <div style="background-color: #f7fafc; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">🔍 SEO Settings</h3>
                
                <div class="form-group">
                    <label class="form-label">SEO Title</label>
                    <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $phishingPage->seo_title) }}" placeholder="Page title for search engines (max 60 characters)" maxlength="60">
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Recommended: 50-60 characters. Leave empty to use page name.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">SEO Description</label>
                    <textarea name="seo_description" class="form-control" rows="3" placeholder="Meta description for search engines (max 160 characters)" maxlength="160">{{ old('seo_description', $phishingPage->seo_description) }}</textarea>
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Recommended: 150-160 characters. This appears in search engine results.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">SEO Keywords</label>
                    <input type="text" name="seo_keywords" class="form-control" value="{{ old('seo_keywords', $phishingPage->seo_keywords) }}" placeholder="Comma-separated keywords">
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Separate keywords with commas. Optional but helps with SEO.
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Type <span style="color: red;">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="phishing" {{ old('type', $phishingPage->type) === 'phishing' ? 'selected' : '' }}>Phishing</option>
                    <option value="awareness" {{ old('type', $phishingPage->type) === 'awareness' ? 'selected' : '' }}>Awareness</option>
                    <option value="training" {{ old('type', $phishingPage->type) === 'training' ? 'selected' : '' }}>Training</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $phishingPage->is_active) ? 'checked' : '' }}>
                    Active (Page will be available for use in campaigns)
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">HTML Content <span style="color: red;">*</span></label>
                <textarea name="html_content" id="html-content" class="form-control" rows="20" required style="font-family: 'Courier New', monospace; font-size: 0.875rem;">{{ old('html_content', $phishingPage->html_content) }}</textarea>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Enter the HTML content for your phishing page. You can use placeholders like <code>&#123;&#123;employee_name&#125;&#125;</code>, <code>&#123;&#123;reset_link&#125;&#125;</code>, etc.
                </small>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Update Phishing Page</button>
                <a href="{{ route('client.phishing-pages') }}" class="btn btn-secondary">Cancel</a>
                <a href="{{ route('client.phishing-pages.preview', $phishingPage->id) }}" class="btn btn-secondary" target="_blank">Preview</a>
            </div>
        </form>
    </div>
</div>
@endsection

