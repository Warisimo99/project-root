<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';


// Fetch the top 6 trending posts
$sql = "SELECT posts.*, COUNT(likes.id) AS like_count
        FROM posts
        LEFT JOIN likes ON posts.id = likes.post_id
        GROUP BY posts.id
        ORDER BY like_count DESC
        LIMIT 6"; 

$result = mysqli_query($conn, $sql);

// Debug Check for query errors
if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

// Debug Check the number of rows returned
$rowCount = mysqli_num_rows($result);

if ($rowCount > 0) {
    $trendingPosts = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $trendingPosts = [];
}
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
    <link rel="stylesheet" href="assets/css/dashstyle.css">
    <title>Dashboard</title>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="post.php">Post</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="blog.php">Blog</a>
        </li>
        </ul>
    </div>
  </div>
</nav>
    <main>
    <div class="container mt-5">
    <h2>Dashboard Overview</h2>
    <span class="badge bg-danger">Trending</span>
    <div class="container mt-5">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</h2>

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

            <!-- Display Trending Posts -->
            <div class="mt-5">
            <div style="display: flex; justify-content: center; padding: 20px;">
                <h2 class="btn btn-outline-light centered-heading">Trending Posts</h2>
            </div>
            <?php if (!empty($trendingPosts)): ?>
            <div class="row">
                <?php foreach ($trendingPosts as $post): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100"> <!-- Added h-100 for equal height cards -->
                            <?php if (!empty($post['image'])): ?>

                                <img src="public/assets/images/<?php echo htmlspecialchars(basename($post['image'])); ?>">

                            <?php endif; ?>
                            <div class="card-body d-flex flex-column"> <!-- Added flex column layout -->
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($post['summary']); ?></p>
                                <p class="card-text mt-auto"> <!-- Pushed to bottom -->
                                    <small class="text-muted">
                                        <i class="fas fa-thumbs-up"></i> <?php echo $post['like_count']; ?> 
                                    </small>
                                </p>
                                <a href="read.php?id=<?php echo $post['id']; ?>" class="btn btn-primary mt-2">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No trending posts found.</p>
        <?php endif; ?>
        </div>
    </div>
    <div style="display: flex; justify-content: center; padding: 20px;">
        <h2 class="btn btn-outline-light centered-heading">News & Screener</h2>
    </div>
        <div class="widgets-container">
        <!-- First Widget (Left) -->
        <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
            <div class="tradingview-widget-copyright">
                <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank">
                    <span class="blue-text">Track all markets on TradingView</span>
                </a>
            </div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-timeline.js" async>
            {
                "feedMode"; "all_symbols",
                "isTransparent"; false,
                "displayMode"; "regular",
                "width"; 400,
                "height"; 550,
                "colorTheme"; "light",
                "locale"; "en"
            }
            </script>
        </div>

        <!-- Second Widget (Right Top) -->
          <div class="tradingview-widget-container">
              <div class="tradingview-widget-container__widget"></div>
              <div class="tradingview-widget-copyright">
                  <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank">
                      <span class="blue-text">Track all markets on TradingView</span>
                  </a>
              </div>
              <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-forex-heat-map.js" async>
              {
                  "width"; 550,
                  "height"; 400,
                  "currencies"; [
                      "EUR",
                      "USD",
                      "JPY",
                      "GBP",
                      "CHF",
                      "AUD",
                      "CAD",
                      "NZD",
                      "CNY"
                  ],
                  "isTransparent"; true,
                  "colorTheme"; "light",
                  "locale"; "en"
              }
              </script>
          </div>

          <!-- Third Widget (Right Bottom) -->
          <div class="tradingview-widget-container">
              <div class="tradingview-widget-container__widget"></div>
              <div class="tradingview-widget-copyright">
                  <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank">
                      <span class="blue-text">Track all markets on TradingView</span>
                  </a>
              </div>
              <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-forex-cross-rates.js" async>
              {
                  "width"; 550,
                  "height"; 400,
                  "currencies"; [
                      "EUR",
                      "USD",
                      "JPY",
                      "GBP",
                      "CHF",
                      "AUD",
                      "CAD",
                      "NZD"
                  ],
                  "isTransparent"; false,
                  "colorTheme"; "light",
                  "locale"; "en",
                  "backgroundColor"; "#ffffff"
              }
              </script>
          </div>
      </div>
    </div>
        <!-- TradingView Widget END -->
    </main>
    <div id="spinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <button class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Loading...</span>
            </button>
        </div>
    </div>

    <script type="module">
        import { initializeSpinner } from '/../public/assets/js/spinner.js';
        initializeSpinner();
    </script>

    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>
<footer class="footer bg-dark text-white py-4">
    <div class="container text-center">
        <p> Creator: Abdul . All rights reserved.</p>
        <p>
            <a href="Help.php" class="text-white">Help</a> |  
            <a href="https://www.tradingview.com/widget-docs/widgets/" class="text-white">Copyright &copy; 2025 TradingView, Inc.</a>
        </p>
    </div>
</footer>

</html>
