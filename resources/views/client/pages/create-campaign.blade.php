@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Flash Messages - Fixed at top --}}
    <div style="position: sticky; top: 80px; z-index: 100; margin-bottom: 1rem;">
        @if(session('success'))
            <div id="flash-message" class="alert alert-success" style="position: relative; padding-right: 2.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; opacity: 0.7; padding: 0; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div id="flash-message" class="alert alert-error" style="position: relative; padding-right: 2.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; opacity: 0.7; padding: 0; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
            </div>
        @endif
    </div>

    <div class="card">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">Create Campaign</h1>
        <p style="color: #718096; margin-bottom: 1.5rem;">Start a new phishing simulation or awareness campaign.</p>

        <form method="POST" action="{{ route('client.campaigns.store') }}" id="campaign-form">
            @csrf
            <div class="form-group">
                <label class="form-label">Type <span style="color: red;">*</span></label>
                <select name="type" id="campaign-type" class="form-control" required onchange="filterTemplates()">
                    <option value="">Select Type</option>
                    <option value="phishing" {{ request('template_type') === 'phishing' ? 'selected' : '' }}>Phishing</option>
                    <option value="awareness" {{ request('template_type') === 'awareness' ? 'selected' : '' }}>Awareness</option>
                    <option value="training" {{ request('template_type') === 'training' ? 'selected' : '' }}>Training</option>
                </select>
                @if(request('template_type'))
                    <small style="color: #2b6cb0; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        ✓ Template type pre-selected
                    </small>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label">Email Template <span style="color: red;">*</span></label>
                <select name="email_template_id" id="email-template" class="form-control" required>
                    <option value="">Select a template first</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" 
                                data-type="{{ $template->type }}"
                                {{ old('email_template_id') == $template->id ? 'selected' : '' }}>
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
                <label class="form-label">Start Date <span style="color: red;">*</span></label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">End Date <span style="color: red;">*</span></label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Campaign</button>
            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary" style="margin-left: .5rem;">Cancel</a>
        </form>

        <script>
            function filterTemplates() {
                const type = document.getElementById('campaign-type').value;
                const templateSelect = document.getElementById('email-template');
                const previewDiv = document.getElementById('template-preview');
                
                // Reset template selection
                templateSelect.value = '';
                previewDiv.style.display = 'none';
                
                // Filter templates by type
                const options = templateSelect.querySelectorAll('option');
                options.forEach(option => {
                    if (option.value === '') {
                        if (type) {
                            option.textContent = 'Select a template';
                        } else {
                            option.textContent = 'Select a template first';
                        }
                        option.style.display = 'block';
                    } else {
                        const templateType = option.getAttribute('data-type');
                        if (type && templateType === type) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
                
                // If type is pre-selected, show first matching template
                if (type) {
                    const firstVisible = Array.from(options).find(opt => 
                        opt.value && opt.style.display !== 'none'
                    );
                    if (firstVisible) {
                        templateSelect.value = firstVisible.value;
                        updatePreview();
                    }
                }
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
                const typeSelect = document.getElementById('campaign-type');
                const templateSelect = document.getElementById('email-template');
                
                // Filter templates if type is already selected
                if (typeSelect.value) {
                    filterTemplates();
                }
                
                // Update preview when template changes
                templateSelect.addEventListener('change', updatePreview);
                
                // Update preview if template is pre-selected
                if (templateSelect.value) {
                    updatePreview();
                }
            });
        </script>

    <script>
        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('#flash-message');
            flashMessages.forEach(function(message) {
                // Add fade-out animation
                message.style.transition = 'opacity 0.5s ease-out';
                
                // Hide after 5 seconds
                setTimeout(function() {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.remove();
                    }, 500); // Wait for fade-out animation
                }, 5000);
            });
        });
    </script>
    </div>
</div>
@endsection



