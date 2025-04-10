<?php
// Start the session
session_start();

$db_path = __DIR__ . '/../includes/db.php';
error_log("looking at: . $db_path");

if(!file_exists($db_path)) {
    die(json_encode([
        'error' => 'Database config missing',
        'debug' => [
            'current_dir' => __DIR__,
            'attempted_path' => $db_path,
            'file_exists' => file_exists($db_path)
        ]
        ]));
}

require_once $db_path;

// Check if the connection was successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if this is a search API request
if (isset($_GET['search']) && $_GET['search'] == '1') {
    header('Content-Type: application/json');
    
    $searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($searchQuery)) {
        echo json_encode(['error' => 'Please enter a search term']);
        exit;
    }

    try {
        $sql = "SELECT p.*, u.fname 
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE MATCH(p.title) AGAINST(? IN BOOLEAN MODE) 
                ORDER BY p.created_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception("SQL preparation failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, 's', $searchQuery);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("SQL execution failed: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        echo json_encode($posts);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}


$sql = "SELECT p.*, u.fname 
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL query failed: " . mysqli_error($conn));
}

$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <title>Blog</title>
    <style>
        #searchResults {
            position: absolute;
            width: 100%;
            max-width: 500px;
            background: white;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: none;
        }
        .search-result {
            padding: 30px;
            border-bottom: 3px solid #eee;
        }
        .search-result:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Your existing navbar with search form -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Posts</a>
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
                <form class="d-flex" role="search" id="searchForm">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search" aria-label="Search" id="searchInput">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                <div id="searchResults"></div>
            </div>
        </div>
    </nav>

    <!-- Your existing container with posts -->
    <div class="container mt-5">
        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <section class="d-flex">
            <main class="flex-grow-1">
                <div style="display: flex; justify-content: center; padding: 20px;">
                    <h2 class="btn btn-outline-light centered-heading">Blog Dashboard</h2>
                </div>
                
                <?php if (isset($_SESSION["create"])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION["create"]); ?></div>
                    <?php unset($_SESSION["create"]); ?>
                <?php endif; ?>

                <div id="postsContainer">
                    <?php if (empty($posts)): ?>
                        <p class="text-center">No posts found.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($posts as $post): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <?php if (!empty($post['image'])): ?>
                                            <!-- Post Image -->
                                            <img src="public/assets/images/<?php echo htmlspecialchars(basename($post['image'])); ?>" class="card-img-top" alt="Post Image">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/300" class="card-img-top" alt="Placeholder Image">
                                        <?php endif; ?>


                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                            <p class="card-text"><strong>Author:</strong> <?php echo htmlspecialchars($post['fname']); ?></p>
                                            <p class="card-text"><strong>Summary:</strong> <?php echo htmlspecialchars($post['summary']); ?></p>
                                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($post['category']); ?></p>
                                            <p class="card-text"><small class="text-muted">Posted on: <?php echo htmlspecialchars($post['created_at']); ?></small></p>
                                            <a href="read.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
            <aside class="ms-4" style="width: 300px;">
                <!-- TradingView Widget -->
                <div class="tradingview-widget-container" style="width: 300px; height: 990px;">
                    <iframe scrolling="no" allowtransparency="true" frameborder="0" src="https://www.tradingview-widget.com/embed-widget/events/#%7B%22width%22%3A510%2C%22height%22%3A600%2C%22isTransparent%22%3Afalse%2C%22utm_source%22%3A%22aces-macbook-pro.local%22%2C%22utm_medium%22%3A%22widget_new%22%2C%22utm_campaign%22%3A%22events%22%2C%22page-uri%22%3A%22aces-macbook-pro.local%2Fproject-root%2Fpublic%2Fblog.php%22%7D" title="events TradingView widget" lang="en" style="box-sizing: border-box; display: block; height: calc(100% - 32px); width: 100%;"></iframe>
                    <div class="tradingview-widget-container__widget"></div>
                    <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
                </div>
            </aside>
        </section>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    
    <script type="module" src="public/assets/js/qsearch.js"></script>
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