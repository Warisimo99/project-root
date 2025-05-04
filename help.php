
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
    <a class="navbar-brand" href="#">Help</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Homepage</a>
        </li>
        </ul>
    </div>
  </div>
</nav>

<div class="container">
    <div style="display: flex; justify-content: center; padding: 20px;">
        <h2 class="btn btn-outline-light centered-heading">Help Page</h2>
    </div>

        <!-- Website Features -->
        <div class="card">
            <div class="card-header">
                Website Features
            </div>
            <div class="card-body">
            <h5>Live Market Information</h5>
            <p>
                Our website provides real-time market data, including stock prices, forex rates, and cryptocurrency trends, 
                powered by third-party tools like TradingView. <strong>All data is for informational purposes only</strong> 
                and must not be construed as financial advice.
            </p>

            <h5>Upload Blog Posts</h5>
            <p>
                Share your market insights, strategies, or analysis via blog posts. 
                <strong>By posting, you agree to our community guidelines:</strong>
                <ul>
                    <li>No financial advice (only general discussion or personal opinions).</li>
                    <li>No market manipulation, scams, or abusive behavior.</li>
                    <li>Always include disclaimers (e.g., "This is my personal view, not advice").</li>
                </ul>
                Violations will result in content removal or account suspension.
            </p>

            <h5>Interact with Likes and Comments</h5>
            <p>
                Engage respectfully with the community. Harmful Content will result in account termination. 
                Posts will be reviewed every 24 hours.
            </p>
        </div>

        <!-- Purpose & Legal Compliance -->
        <div class="card">
            <div class="card-header">
                Purpose of the Website
            </div>
            <div class="card-body">
                <p>
                    This platform connects traders to share <strong>educational content and opinions</strong> about markets. 
                    <span class="text-danger">
                        <strong>Never interpret discussions as financial advice.</strong> Trading carries risks; 
                        always consult a licensed professional.
                    </span>
                </p>
                <p>
                    We comply with the <strong>Financial Services and Markets Act 2000 (FSMA)</strong> and 
                    <strong>FCA regulations</strong>. Users must avoid:
                </p>
                <ul>
                    <li>Providing regulated financial advice (unauthorized activity).</li>
                    <li>Sharing misleading information or pump-and-dump schemes.</li>
                    <li>Violating third-party terms (e.g., TradingView's API policies).</li>
                </ul>
            </div>
        </div>

        <!-- Copyright & Licensing -->
        <div class="card">
            <div class="card-header">
                Copyright Credits & Licensing
            </div>
            <div class="card-body">
                <p>
                    This site uses licensed third-party tools. By using them, you agree to their terms:
                </p>
                <ul>
                    <li>
                        <strong>TradingView Widgets:</strong> Charts are © <a href="https://www.tradingview.com/" target="_blank">TradingView</a>. 
                        Embedding complies with their <a href="https://www.tradingview.com/terms/" target="_blank">Terms of Service</a>.
                    </li>
                    <li>
                        <strong>Font Awesome Icons:</strong> © <a href="https://fontawesome.com/" target="_blank">Font Awesome</a> 
                        (used under <a href="https://fontawesome.com/license" target="_blank">free license</a>).
                    </li>
                    <li>
                        <strong>MyFxBook Widgets:</strong> Data provided by <a href="https://www.myfxbook.com" target="_blank">MyFxBook</a> 
                        under their <a href="https://www.myfxbook.com/terms" target="_blank">widget policy</a>.
                    </li>
                </ul>
                <p class="mt-3">
                    <strong>User-generated content:</strong> Posts default to <a href="https://creativecommons.org/licenses/by-nc/4.0/" target="_blank">CC BY-NC 4.0</a> 
                    unless specified otherwise. You retain ownership but grant the platform sharing rights.
                </p>
            </div>
        </div>

        <!-- User Safety & Wellbeing -->
        <div class="card">
            <div class="card-header">
                Safety & Wellbeing Resources
            </div>
            <div class="card-body">
                <h5>Protecting Yourself & Others</h5>
                <ul>
                    <li>Verify information independently before acting on it.</li>
                    <li>Avoid sharing personal financial details.</li>
                    <li>Report suspicious activity via <a href="/contact">Contact Form</a>.</li>
                </ul>
                <h5>Mental Health Support</h5>
                <p>
                    Trading stress is real. Free confidential help:
                    <a href="https://www.mind.org.uk" target="_blank">Mind UK</a> | 
                    <a href="https://www.samaritans.org" target="_blank">Samaritans</a>.
                </p>
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
        import { initializeSpinner } from '/../public/assets/js/spinner.js';
        initializeSpinner();
    </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</body>


</html>
