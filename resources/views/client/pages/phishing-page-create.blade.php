@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">🕵️ Create Phishing Page</h1>
        <p style="color: #718096; margin-bottom: 1.5rem;">Create a new phishing landing page for your campaigns.</p>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('client.phishing-pages.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Name <span style="color: red;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g., Password Reset Page">
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this phishing page">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">URL Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Auto-generated from name (e.g., password-reset-page)">
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Leave empty to auto-generate from name. Used for public URL: {{ config('app.url') }}/page/<strong>your-slug</strong>
                </small>
            </div>

            <div style="background-color: #f7fafc; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">🔍 SEO Settings</h3>
                
                <div class="form-group">
                    <label class="form-label">SEO Title</label>
                    <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title') }}" placeholder="Page title for search engines (max 60 characters)" maxlength="60">
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Recommended: 50-60 characters. Leave empty to use page name.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">SEO Description</label>
                    <textarea name="seo_description" class="form-control" rows="3" placeholder="Meta description for search engines (max 160 characters)" maxlength="160">{{ old('seo_description') }}</textarea>
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Recommended: 150-160 characters. This appears in search engine results.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">SEO Keywords</label>
                    <input type="text" name="seo_keywords" class="form-control" value="{{ old('seo_keywords') }}" placeholder="Comma-separated keywords (e.g., password, reset, account)">
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Separate keywords with commas. Optional but helps with SEO.
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Type <span style="color: red;">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="phishing" {{ old('type') === 'phishing' ? 'selected' : '' }}>Phishing</option>
                    <option value="awareness" {{ old('type') === 'awareness' ? 'selected' : '' }}>Awareness</option>
                    <option value="training" {{ old('type') === 'training' ? 'selected' : '' }}>Training</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    Active (Page will be available for use in campaigns)
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">HTML Content <span style="color: red;">*</span></label>
                <textarea name="html_content" id="html-content" class="form-control" rows="20" required style="font-family: 'Courier New', monospace; font-size: 0.875rem;">{{ old('html_content', '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #2b6cb0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Verification Required</h1>
            <p>Please verify your account to continue</p>
        </div>
        <form id="phishing-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Verify Account</button>
        </form>
    </div>
</body>
</html>') }}</textarea>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Enter the HTML content for your phishing page. You can use placeholders like <code>&#123;&#123;employee_name&#125;&#125;</code>, <code>&#123;&#123;reset_link&#125;&#125;</code>, etc.
                </small>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Create Phishing Page</button>
                <a href="{{ route('client.phishing-pages') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

