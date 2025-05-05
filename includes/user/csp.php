<?php
// Generate nonce for inline elements
$nonce = base64_encode(random_bytes(16));

// Single-line CSP header (remove all actual line breaks)
header("Content-Security-Policy: default-src 'self'; base-uri 'self'; script-src 'self' 'nonce-$nonce' 'unsafe-eval' https://*.tradingview.com https://cdn.jsdelivr.net https://kit.fontawesome.com https://ka-f.fontawesome.com https://cdnjs.cloudflare.com https://unpkg.com https://widgetdata.tradingview.com https://pushstream.tradingview.com; style-src 'self' 'nonce-$nonce' 'unsafe-inline' https://*.tradingview.com https://cdn.jsdelivr.net https://kit.fontawesome.com https://ka-f.fontawesome.com https://fonts.googleapis.com https://unpkg.com https://cdnjs.cloudflare.com; img-src 'self' data: blob: https://*.tradingview.com https://thumbs.dreamstime.com https://s.tradingview.com https://s3.tradingview.com https://telemetry.tradingview.com; font-src 'self' data: https://kit.fontawesome.com https://ka-f.fontawesome.com https://cdn.jsdelivr.net https://fonts.gstatic.com; connect-src 'self' https://*.tradingview.com wss://*.tradingview.com https://ka-f.fontawesome.com https://s.tradingview.com https://s3.tradingview.com https://widgetdata.tradingview.com wss://widgetdata.tradingview.com https://pushstream.tradingview.com wss://pushstream.tradingview.com https://telemetry.tradingview.com; frame-src 'self' https://*.tradingview.com https://www.tradingview-widget.com; object-src 'none'; worker-src 'none';");

// Store nonce as constant for use in templates
define('CSP_NONCE', $nonce);
?>