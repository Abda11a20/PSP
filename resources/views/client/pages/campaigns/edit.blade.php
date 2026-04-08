@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 700;">Edit Campaign</h1>
            <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-secondary">← Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('client.campaigns.update', $campaign->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Campaign Type <span style="color: #e53e3e;">*</span></label>
                <select name="type" id="campaign-type" class="form-control" required onchange="filterTemplates()">
                    <option value="phishing" {{ $campaign->type === 'phishing' ? 'selected' : '' }}>Phishing</option>
                    <option value="awareness" {{ $campaign->type === 'awareness' ? 'selected' : '' }}>Awareness</option>
                    <option value="training" {{ $campaign->type === 'training' ? 'selected' : '' }}>Training</option>
                </select>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Select the type of campaign you want to create
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Email Template <span style="color: #e53e3e;">*</span></label>
                <select name="email_template_id" id="email-template" class="form-control" required>
                    <option value="">Select a template</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" 
                                data-type="{{ $template->type }}"
                                {{ old('email_template_id', $campaign->email_template_id) == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} ({{ ucfirst($template->type) }})
                        </option>
                    @endforeach
                </select>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Choose an email template for this campaign. Templates are filtered by campaign type.
                </small>
                <div id="template-preview" style="margin-top: 1rem; padding: 1rem; background-color: #f7fafc; border-radius: 0.5rem; display: none;">
                    <a href="#" id="preview-link" target="_blank" class="btn btn-secondary" style="text-decoration: none;">
                        👁️ Preview Selected Template
                    </a>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Start Date <span style="color: #e53e3e;">*</span></label>
                <input type="date" name="start_date" class="form-control" 
                       value="{{ $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '' }}" 
                       required>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    When should this campaign start?
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">End Date <span style="color: #e53e3e;">*</span></label>
                <input type="date" name="end_date" class="form-control" 
                       value="{{ $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '' }}" 
                       required>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    When should this campaign end? (Must be after start date)
                </small>
            </div>

            <div style="background-color: #f7fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">Campaign Status</h4>
                <p style="color: #718096; font-size: 0.875rem; margin: 0;">
                    Current Status: <strong style="text-transform: capitalize;">{{ $campaign->status }}</strong>
                </p>
                @if($campaign->status !== 'draft')
                    <p style="color: #e53e3e; font-size: 0.875rem; margin-top: 0.5rem; margin-bottom: 0;">
                        ⚠️ Only draft campaigns can be edited. You can only change the type and dates.
                    </p>
                @endif
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Update Campaign</button>
                <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-secondary">Cancel</a>
                @if($campaign->status === 'draft')
                    <form method="POST" action="{{ route('client.campaigns.destroy', $campaign->id) }}" style="display: inline; margin-left: auto;" onsubmit="return confirm('Are you sure you want to delete this campaign? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Campaign</button>
                    </form>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    function filterTemplates() {
        const type = document.getElementById('campaign-type').value;
        const templateSelect = document.getElementById('email-template');
        const previewDiv = document.getElementById('template-preview');
        
        // Filter templates by type
        const options = templateSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const templateType = option.getAttribute('data-type');
                if (templateType === type) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    // If current selection doesn't match type, clear it
                    if (option.selected) {
                        templateSelect.value = '';
                        previewDiv.style.display = 'none';
                    }
                }
            }
        });
    }
    
    function updatePreview() {
        const templateId = document.getElementById('email-template').value;
        const previewDiv = document.getElementById('template-preview');
        const previewLink = document.getElementById('preview-link');
        
        if (templateId) {
            previewLink.href = '{{ route("client.templates.preview", ":id") }}'.replace(':id', templateId);
            previewDiv.style.display = 'block';
        } else {
            previewDiv.style.display = 'none';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        filterTemplates();
        const templateSelect = document.getElementById('email-template');
        templateSelect.addEventListener('change', updatePreview);
        if (templateSelect.value) {
            updatePreview();
        }
    });
</script>
@endsection


