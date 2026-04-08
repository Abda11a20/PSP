<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Preview: {{ $template->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f7fafc;
            padding: 2rem;
        }
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .preview-header {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .preview-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .preview-header p {
            color: #718096;
            margin-bottom: 1rem;
        }
        .preview-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #2b6cb0;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2c5282;
        }
        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background-color: #cbd5e0;
        }
        .preview-content {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .preview-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            background-color: #f7fafc;
        }
        .preview-tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border: none;
            background: none;
            font-weight: 600;
            color: #718096;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .preview-tab.active {
            color: #2b6cb0;
            border-bottom-color: #2b6cb0;
            background-color: white;
        }
        .preview-tab:hover {
            color: #2b6cb0;
        }
        .preview-body {
            padding: 2rem;
        }
        .preview-html {
            min-height: 400px;
            display: block;
        }
        .preview-html.hidden {
            display: none;
        }
        .preview-html iframe {
            width: 100%;
            min-height: 600px;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
        }
        .preview-source {
            display: none;
        }
        .preview-source.active {
            display: block;
        }
        .preview-source textarea {
            width: 100%;
            min-height: 600px;
            background-color: #1a202c;
            color: #e2e8f0;
            padding: 1.5rem;
            border-radius: 0.375rem;
            border: 1px solid #2d3748;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.875rem;
            line-height: 1.6;
            resize: vertical;
            outline: none;
        }
        .preview-source textarea:focus {
            border-color: #2b6cb0;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
        }
        .source-actions {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            justify-content: flex-end;
        }
        .save-message {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .save-message.success {
            background-color: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        .save-message.error {
            background-color: #fed7d7;
            color: #c53030;
            border: 1px solid #fc8181;
        }
        .save-message.loading {
            background-color: #bee3f8;
            color: #2c5282;
            border: 1px solid #90cdf4;
        }
        #save-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .template-info {
            background-color: #f7fafc;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
        .template-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-size: 0.75rem;
            color: #718096;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-phishing {
            background-color: #fed7d7;
            color: #c53030;
        }
        .badge-awareness {
            background-color: #bee3f8;
            color: #2c5282;
        }
        .badge-training {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .help-panel {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease-out;
            overflow-y: auto;
        }
        .help-panel.open {
            max-height: 800px;
        }
        .help-header {
            background-color: #2b6cb0;
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .help-content {
            padding: 1.5rem;
        }
        .help-section {
            margin-bottom: 2rem;
        }
        .help-section h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .placeholder-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .placeholder-item {
            background-color: #f7fafc;
            padding: 1rem;
            border-radius: 0.375rem;
            border-left: 3px solid #2b6cb0;
        }
        .placeholder-code {
            font-family: 'Courier New', Courier, monospace;
            background-color: #1a202c;
            color: #68d391;
            padding: 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        .placeholder-desc {
            font-size: 0.875rem;
            color: #4a5568;
            line-height: 1.5;
        }
        .placeholder-example {
            font-size: 0.75rem;
            color: #718096;
            margin-top: 0.5rem;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>📧 Email Template Preview</h1>
            <p>{{ $template->name }}</p>
            
            <div class="template-info">
                <div class="template-info-grid">
                    <div class="info-item">
                        <span class="info-label">Template Name</span>
                        <span class="info-value">{{ $template->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Type</span>
                        <span class="badge badge-{{ $template->type }}">{{ ucfirst($template->type) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created</span>
                        <span class="info-value">{{ $template->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="preview-actions">
                <a href="{{ route('client.templates.use', $template->id) }}" class="btn btn-primary">
                    ✨ Use This Template
                </a>
                <a href="{{ route('client.templates') }}" class="btn btn-secondary">
                    ← Back to Templates
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print Preview
                </button>
                <button onclick="toggleHelp()" class="btn btn-secondary" id="help-btn">
                    📖 Available Placeholders
                </button>
            </div>
        </div>

        <div class="help-panel" id="help-panel">
            <div class="help-header" onclick="toggleHelp()">
                <span>📖 Available Placeholders Guide</span>
                <span id="help-toggle">▼</span>
            </div>
            <div class="help-content">
                <div class="help-section">
                    <h3>👤 User Information</h3>
                    <div class="placeholder-list">
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{name}}</div>
                            <div class="placeholder-desc">Target's full name</div>
                            <div class="placeholder-example">Example: "John Doe"</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{email}}</div>
                            <div class="placeholder-desc">Target's email address</div>
                            <div class="placeholder-example">Example: "john.doe@company.com"</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{employee_name}}</div>
                            <div class="placeholder-desc">Target's name (alias for @{{name}})</div>
                            <div class="placeholder-example">Example: "John Doe"</div>
                        </div>
                    </div>
                </div>

                <div class="help-section">
                    <h3>🔗 Link Placeholders</h3>
                    <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1rem;">
                        All link placeholders are automatically replaced with phishing tracking links when emails are sent.
                    </p>
                    <div class="placeholder-list">
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{fake_link}}</div>
                            <div class="placeholder-desc">Generic fake link for phishing simulation</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{reset_link}}</div>
                            <div class="placeholder-desc">Password reset link</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{login_link}}</div>
                            <div class="placeholder-desc">Login/authentication link</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{verify_link}}</div>
                            <div class="placeholder-desc">Account verification link</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{verification_link}}</div>
                            <div class="placeholder-desc">Account verification link (alternative)</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{training_link}}</div>
                            <div class="placeholder-desc">Training/educational resource link</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{status_link}}</div>
                            <div class="placeholder-desc">System status page link</div>
                        </div>
                    </div>
                </div>

                <div class="help-section">
                    <h3>📅 Date Placeholders</h3>
                    <div class="placeholder-list">
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{deadline}}</div>
                            <div class="placeholder-desc">Deadline date (7 days from now)</div>
                            <div class="placeholder-example">Format: "December 14, 2024"</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{maintenance_date}}</div>
                            <div class="placeholder-desc">Maintenance date (3 days from now)</div>
                            <div class="placeholder-example">Format: "December 10, 2024"</div>
                        </div>
                    </div>
                </div>

                <div class="help-section">
                    <h3>⚙️ System Placeholders</h3>
                    <div class="placeholder-list">
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{tracking_pixel}}</div>
                            <div class="placeholder-desc">Invisible tracking pixel for email open tracking</div>
                            <div class="placeholder-example">Automatically inserted as 1x1 image</div>
                        </div>
                        <div class="placeholder-item">
                            <div class="placeholder-code">@{{campaign_name}}</div>
                            <div class="placeholder-desc">Campaign type/name</div>
                            <div class="placeholder-example">Example: "phishing" or "awareness"</div>
                        </div>
                    </div>
                </div>

                <div class="help-section">
                    <h3>💡 Usage Tips</h3>
                    <ul style="color: #4a5568; line-height: 1.8; padding-left: 1.5rem;">
                        <li>Placeholders are case-sensitive. Use exact format: <code style="background: #f7fafc; padding: 0.2rem 0.4rem; border-radius: 0.25rem;">@{{placeholder_name}}</code></li>
                        <li>In preview mode, links show as <code>#</code> and dates show sample values</li>
                        <li>When emails are sent, placeholders are automatically replaced with real data</li>
                        <li>All link placeholders track clicks for phishing simulation analytics</li>
                        <li>You can use multiple placeholders in a single template</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="preview-content">
            <div class="preview-tabs">
                <button class="preview-tab active" onclick="showTab('preview')">Preview</button>
                <button class="preview-tab" onclick="showTab('source')">HTML Source</button>
            </div>
            
            <div class="preview-body">
                <div id="preview-tab" class="preview-html">
                    <iframe srcdoc="{{ $previewContent }}" frameborder="0"></iframe>
                </div>
                <div id="source-tab" class="preview-source">
                    <div class="source-actions">
                        <button onclick="saveTemplate()" class="btn btn-primary" id="save-btn">
                            💾 Save to Database
                        </button>
                        <button onclick="updatePreview()" class="btn btn-secondary">
                            🔄 Update Preview
                        </button>
                        <button onclick="resetSource()" class="btn btn-secondary">
                            ↺ Reset
                        </button>
                    </div>
                    <div id="save-message" style="display: none; margin-bottom: 1rem; padding: 0.75rem; border-radius: 0.375rem; font-size: 0.875rem;"></div>
                    <textarea id="html-source-editor" spellcheck="false">{!! $template->html_content !!}</textarea>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store original HTML content
        let originalHtmlContent = @json($template->html_content);
        const templateId = {{ $template->id }};
        const updateUrl = '{{ route("client.templates.update", $template->id) }}';
        const csrfToken = '{{ csrf_token() }}';
        
        function showTab(tab) {
            const previewTab = document.getElementById('preview-tab');
            const sourceTab = document.getElementById('source-tab');
            const tabButtons = document.querySelectorAll('.preview-tab');
            
            // Remove active class from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Hide all content tabs
            previewTab.classList.add('hidden');
            sourceTab.classList.remove('active');
            
            // Show selected tab
            if (tab === 'preview') {
                previewTab.classList.remove('hidden');
                tabButtons[0].classList.add('active');
            } else {
                sourceTab.classList.add('active');
                tabButtons[1].classList.add('active');
            }
        }
        
        function showMessage(message, type) {
            const messageDiv = document.getElementById('save-message');
            messageDiv.textContent = message;
            messageDiv.className = 'save-message ' + type;
            messageDiv.style.display = 'block';
            
            // Auto-hide after 5 seconds for success/error messages
            if (type !== 'loading') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }
        
        function hideMessage() {
            document.getElementById('save-message').style.display = 'none';
        }
        
        async function saveTemplate() {
            const editor = document.getElementById('html-source-editor');
            const saveBtn = document.getElementById('save-btn');
            const htmlContent = editor.value;
            
            if (!htmlContent.trim()) {
                showMessage('HTML content cannot be empty!', 'error');
                return;
            }
            
            // Disable save button and show loading
            saveBtn.disabled = true;
            showMessage('Saving template...', 'loading');
            
            try {
                const response = await fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        html_content: htmlContent
                    })
                });
                
                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    // Handle non-JSON response (redirect, etc.)
                    if (response.ok) {
                        showMessage('✅ Template saved successfully to database!', 'success');
                        originalHtmlContent = htmlContent;
                        return;
                    } else {
                        throw new Error('Unexpected response format');
                    }
                }
                
                if (response.ok && data.success) {
                    showMessage('✅ Template saved successfully to database!', 'success');
                    // Update original content to current saved content
                    originalHtmlContent = htmlContent;
                } else {
                    const errorMessage = data.message || data.error || 'Failed to save template';
                    showMessage('❌ ' + errorMessage, 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                showMessage('❌ An error occurred while saving. Please try again.', 'error');
            } finally {
                saveBtn.disabled = false;
            }
        }
        
        function updatePreview() {
            const editor = document.getElementById('html-source-editor');
            const iframe = document.querySelector('#preview-tab iframe');
            const htmlContent = editor.value;
            
            // Replace placeholders with sample data (same as backend)
            let previewContent = htmlContent;
            
            // User information
            previewContent = previewContent.replace(/\{\{name\}\}/g, 'John Doe');
            previewContent = previewContent.replace(/\{\{email\}\}/g, 'john.doe@company.com');
            previewContent = previewContent.replace(/\{\{employee_name\}\}/g, 'John Doe');
            
            // Link placeholders (all replaced with # for preview)
            previewContent = previewContent.replace(/\{\{fake_link\}\}/g, '#');
            previewContent = previewContent.replace(/\{\{reset_link\}\}/g, '#');
            previewContent = previewContent.replace(/\{\{login_link\}\}/g, '#');
            previewContent = previewContent.replace(/\{\{verify_link\}\}/g, '#');
            previewContent = previewContent.replace(/\{\{verification_link\}\}/g, '#');
            previewContent = previewContent.replace(/\{\{training_link\}\}/g, '#');
            previewContent = previewContent.replace(/\{\{status_link\}\}/g, '#');
            
            // Date placeholders
            const deadlineDate = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
            const maintenanceDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);
            previewContent = previewContent.replace(/\{\{deadline\}\}/g, deadlineDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }));
            previewContent = previewContent.replace(/\{\{maintenance_date\}\}/g, maintenanceDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }));
            
            // System placeholders
            previewContent = previewContent.replace(/\{\{tracking_pixel\}\}/g, '<img src="#" width="1" height="1" style="display:none;" />');
            previewContent = previewContent.replace(/\{\{campaign_name\}\}/g, 'phishing');
            
            // Update iframe content
            iframe.srcdoc = previewContent;
            
            // Show preview tab
            showTab('preview');
        }
        
        function resetSource() {
            const editor = document.getElementById('html-source-editor');
            if (confirm('Are you sure you want to reset the HTML source to the original? All your changes will be lost.')) {
                editor.value = originalHtmlContent;
                hideMessage();
            }
        }
        
        function toggleHelp() {
            const helpPanel = document.getElementById('help-panel');
            const helpToggle = document.getElementById('help-toggle');
            helpPanel.classList.toggle('open');
            helpToggle.textContent = helpPanel.classList.contains('open') ? '▲' : '▼';
        }
    </script>
</body>
</html>


