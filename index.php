<?php
// config.php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
session_start(); // Start session here or later in individual files
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c8d66c9de4.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="public/assets/css/dashstyle.css">
    <title>Sidebar With Bootstrap</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="public/assets/css/home.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <style>
        /* Flexbox Layout for Body */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Main Content Styling */
        main > :not(.hero-section) {
            flex: 1;
            padding: 40px; /* Add padding to the main content */
            padding-bottom: 80px; /* Extra padding at the bottom */
        }

    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">DashView</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="public/dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="public/help.php">Help</a>
        </li>
        <li class="nav-item">
        <a class="nav-link active text-bg-info p-2" aria-current="page" href="public/register.php">Register | Login</a>
        </li>
        </div>
        </ul>
    </div>
  </div>
</nav>
    <main>

    <section class="hero-section">
        <div class="hero-container" data-aos="fade-up" data-aos-duration="1000">
            <div class="hero-content">
                <h1>Welcome to DashView</h1>
                <p>Your platform for market updates,</p>
                </b></b> 
                <p>trading insights and community posts.</p>
                <a href="register.php" class="hero-button">Get Started</a>
            </div>
            <div class="hero-image"></div>
        </div>
    </section>

    <!-- Bootstrap Carousel -->
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" id="carousel-inner">
                <!-- Slides will be dynamically added here -->
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

    <!-- TradingView Widget BEGIN -->
    <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
            {
            "symbols"; [
                {
                "proName": "FOREXCOM:SPXUSD",
                "title": "S&P 500 Index"
                },
                {
                "proName": "FOREXCOM:NSXUSD",
                "title": "US 100 Cash CFD"
                },
                {
                "proName": "FX_IDC:EURUSD",
                "title": "EUR to USD"
                },
                {
                "proName": "BITSTAMP:BTCUSD",
                "title": "Bitcoin"
                },
                {
                "proName": "BITSTAMP:ETHUSD",
                "title": "Ethereum"
                }
            ],
            "showSymbolLogo"; true,
            "isTransparent"; false,
            "displayMode"; "adaptive",
            "colorTheme"; "light",
            "locale"; "en"
            }
            </script>
            </div>
            <!-- TradingView Widget END -->

    </main>
    <div class="flex-grow-1">
    <h2 class="text-center mb-4" style="padding-bottom: 25px;padding-top: 35px;">
                <span class="rounded-pill bg-light p-2 custom-text">Technical Analysis</span>
            </h2>
    </div>
    <div class="widget-container">
        <!-- TradingView Widget BEGIN -->
        <div class="tradingview-widget-container" style="height:600px;width:100%">
        <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
        {
            "autosize"; true,
            "symbol"; "NASDAQ:AAPL",
            "interval"; "D",
            "timezone"; "exchange",
            "theme"; "light",
            "backgroundColor"; "rgba(255, 255, 255, 1)",
            "style"; "1",
            "withdateranges"; true,
            "hide_side_toolbar"; false,
            "allow_symbol_change"; true,
            "save_image"; false,
            "studies"; [
                "ROC@tv-basicstudies",
                "StochasticRSI@tv-basicstudies",
                "MASimple@tv-basicstudies"
            ],
            "locale"; "en",
            "show_popup_button"; true,
            "popup_width"; "1000",
            "popup_height"; "650",
            "calendar"; false,
            "support_host"; "https://www.tradingview.com"
        }
        </script>
        </div>
    </div>
    <!-- TradingView Widget END -->

    <div id="spinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Loading...</span>
            </button>
        </div>
    </div>

    <script type="module">
        import { initializeSpinner } from 'public/assets/js/spinner.js';
        initializeSpinner();
    </script>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script src="public/assets/js/slides.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init();
    </script>
</body>
<footer class="footer bg-dark text-white py-4">
    <div class="container text-center">
        <p> Creator: Abdul . All rights reserved.</p>
        <p>
            <a href="public/help.php" class="text-white">Help</a> |  
            <a href="https://www.tradingview.com/widget-docs/widgets/" class="text-white">Copyright &copy; 2025 TradingView, Inc.</a>
        </p>
    </div>
</footer>


</html>
