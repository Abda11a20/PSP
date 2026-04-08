<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phishing Page Preview: {{ $phishingPage->name }}</title>
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
        .preview-body {
            padding: 2rem;
        }
        .preview-html {
            min-height: 400px;
            display: block;
        }
        .preview-html iframe {
            width: 100%;
            min-height: 600px;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
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
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>🕵️ Phishing Page Preview</h1>
            <p>{{ $phishingPage->name }}</p>
            
            <div class="template-info">
                <div class="template-info-grid">
                    <div class="info-item">
                        <span class="info-label">Page Name</span>
                        <span class="info-value">{{ $phishingPage->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Type</span>
                        <span class="badge badge-{{ $phishingPage->type }}">{{ ucfirst($phishingPage->type) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">{{ $phishingPage->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created</span>
                        <span class="info-value">{{ $phishingPage->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="preview-actions">
                <a href="{{ route('client.phishing-pages.edit', $phishingPage->id) }}" class="btn btn-primary">
                    ✏️ Edit Page
                </a>
                <a href="{{ route('client.phishing-pages') }}" class="btn btn-secondary">
                    ← Back to Phishing Pages
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print Preview
                </button>
            </div>
        </div>

        <div class="preview-content">
            <div class="preview-body">
                <div class="preview-html">
                    <iframe srcdoc="{{ htmlspecialchars($previewContent) }}" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



