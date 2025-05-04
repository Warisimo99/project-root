<?php
// Generate a random nonce for inline scripts/styles (optional but recommended)
$nonce = base64_encode(random_bytes(16));

// Full CSP Header with all required domains
header("Content-Security-Policy: 
    default-src 'self';
    script-src 'self' 'nonce-$nonce' 'unsafe-eval' 
        https://*.tradingview.com 
        https://cdn.jsdelivr.net 
        https://kit.fontawesome.com 
        https://ka-f.fontawesome.com 
        https://cdnjs.cloudflare.com 
        https://unpkg.com;
    style-src 'self' 'nonce-$nonce' 
        https://*.tradingview.com 
        https://cdn.jsdelivr.net 
        https://kit.fontawesome.com 
        https://ka-f.fontawesome.com 
        https://fonts.googleapis.com 
        https://unpkg.com 
        https://cdnjs.cloudflare.com;
    img-src 'self' data: 
        https://*.tradingview.com 
        https://thumbs.dreamstime.com 
        https://s.tradingview.com 
        https://s3.tradingview.com;
    font-src 'self' data: 
        https://kit.fontawesome.com 
        https://ka-f.fontawesome.com 
        https://cdn.jsdelivr.net 
        https://fonts.gstatic.com;
    connect-src 'self' 
        https://*.tradingview.com 
        wss://*.tradingview.com 
        https://ka-f.fontawesome.com 
        https://s.tradingview.com 
        https://s3.tradingview.com;
    frame-src 'self' 
        https://*.tradingview.com 
        https://www.tradingview-widget.com;
    object-src 'none';
    worker-src 'none';
");

// Store nonce for use in templates
define('CSP_NONCE', $nonce);
?>