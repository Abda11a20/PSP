@php
    $htmlContent = $previewContent;
    
    // If content is a full HTML document, inject SEO meta tags into the head
    if (preg_match('/<head[^>]*>/i', $htmlContent)) {
        // Find the closing </head> tag
        $headEnd = strpos($htmlContent, '</head>');
        if ($headEnd !== false) {
            $seoMeta = '
    <!-- SEO Meta Tags -->
    <title>' . htmlspecialchars($phishingPage->seo_title ?: $phishingPage->name) . ' | ' . htmlspecialchars(config('app.name', 'Phishing Simulation Platform')) . '</title>
    <meta name="description" content="' . htmlspecialchars($phishingPage->seo_description ?: ($phishingPage->description ?: 'Phishing simulation page')) . '">
    ' . ($phishingPage->seo_keywords ? '<meta name="keywords" content="' . htmlspecialchars($phishingPage->seo_keywords) . '">' : '') . '
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="' . htmlspecialchars(url()->current()) . '">
    <meta property="og:title" content="' . htmlspecialchars($phishingPage->seo_title ?: $phishingPage->name) . '">
    <meta property="og:description" content="' . htmlspecialchars($phishingPage->seo_description ?: ($phishingPage->description ?: 'Phishing simulation page')) . '">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="' . htmlspecialchars(url()->current()) . '">
    <meta property="twitter:title" content="' . htmlspecialchars($phishingPage->seo_title ?: $phishingPage->name) . '">
    <meta property="twitter:description" content="' . htmlspecialchars($phishingPage->seo_description ?: ($phishingPage->description ?: 'Phishing simulation page')) . '">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="' . htmlspecialchars(url()->current()) . '">
    
    <!-- Robots -->
    <meta name="robots" content="noindex, nofollow">
';
            $htmlContent = substr_replace($htmlContent, $seoMeta, $headEnd, 0);
        }
    } else {
        // Content is just body content, wrap it with full HTML structure
        $htmlContent = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>' . htmlspecialchars($phishingPage->seo_title ?: $phishingPage->name) . ' | ' . htmlspecialchars(config('app.name', 'Phishing Simulation Platform')) . '</title>
    <meta name="description" content="' . htmlspecialchars($phishingPage->seo_description ?: ($phishingPage->description ?: 'Phishing simulation page')) . '">
    ' . ($phishingPage->seo_keywords ? '<meta name="keywords" content="' . htmlspecialchars($phishingPage->seo_keywords) . '">' : '') . '
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="' . htmlspecialchars(url()->current()) . '">
    <meta property="og:title" content="' . htmlspecialchars($phishingPage->seo_title ?: $phishingPage->name) . '">
    <meta property="og:description" content="' . htmlspecialchars($phishingPage->seo_description ?: ($phishingPage->description ?: 'Phishing simulation page')) . '">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="' . htmlspecialchars(url()->current()) . '">
    <meta property="twitter:title" content="' . htmlspecialchars($phishingPage->seo_title ?: $phishingPage->name) . '">
    <meta property="twitter:description" content="' . htmlspecialchars($phishingPage->seo_description ?: ($phishingPage->description ?: 'Phishing simulation page')) . '">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="' . htmlspecialchars(url()->current()) . '">
    
    <!-- Robots -->
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
' . $htmlContent . '
</body>
</html>';
    }
    
    echo $htmlContent;
@endphp
