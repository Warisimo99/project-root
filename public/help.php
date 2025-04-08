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
    <title>Sidebar With Bootstrap</title>
    <style>
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }
        .card-body {
            padding: 20px;
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
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<div class="container">
    <div style="display: flex; justify-content: center; padding: 20px;">
        <h2 class="btn btn-outline-light centered-heading">Help Page</h2>
    </div>

        <!-- Section 1: Website Features -->
        <div class="card">
            <div class="card-header">
                Website Features
            </div>
            <div class="card-body">
                <h5>Live Market Information</h5>
                <p>
                    Our website provides real-time market data, including stock prices, forex rates, and cryptocurrency trends. 
                    You can check live market information using the interactive charts and widgets available on the dashboard.
                </p>

                <h5>Upload Blog Posts</h5>
                <p>
                    Users can create and upload blog posts to share their insights, strategies, and market analysis. 
                    Simply navigate to the "Create Post" section, write your content, and publish it for others to see.
                </p>

                <h5>Interact with Likes and Comments</h5>
                <p>
                    Engage with the community by liking and commenting on blog posts. 
                    Your feedback and interactions help build a vibrant and collaborative trading community.
                </p>
            </div>
        </div>

        <!-- Section 2: Purpose of the Website -->
        <div class="card">
            <div class="card-header">
                Purpose of the Website
            </div>
            <div class="card-body">
                <p>
                    This website is designed to provide a platform for aspiring traders to access live market information, 
                    share their knowledge through blog posts, and interact with a community of like-minded individuals. </p>
                  </br><br>

                <p> Additionally, it serves as a project to test and demonstrate robust security measures to protect user data and ensure a safe online environment.</p>
            </div>
        </div>

        <!-- Section 3: Copyright Credits -->
        <div class="card">
            <div class="card-header">
                Copyright Credits
            </div>
            <div class="card-body">
                <p>
                    The following widgets and tools are used on this website and are copyrighted by their respective owners, also completely free to use:
                </p>
                <ul>

                    <li>
                        <strong>TradingView Widgets:</strong> Charts and market data widgets are provided by 
                        <a href="https://www.tradingview.com/" target="_blank">TradingView</a>. 
                    </li>
                    </br></br>
                    <li>
                        <strong>Font Awesome Icons:</strong> Icons used on this website are provided by 
                        <a href="https://fontawesome.com/" target="_blank">Font Awesome</a>.
                    </li>
                    </br></br>
                    <li>
                        <strong>My FxBook:</strong> Market data widgets are provided by 
                        <a href="https://www.myfxbook.com/get-widget/12/general_widgets/" target="_blank">My FxBook</a>.
                    </li>
                </ul>
            </div>
        </div>
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
        import { initializeSpinner } from '/assets/js/spinner.js';
        initializeSpinner();
    </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>


</html>
