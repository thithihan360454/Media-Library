<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            text-align: center;
            padding-top: 80px;
        }

        .box {
            background: white;
            display: inline-block;
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 700px;
        }

        h1 {
            color: #d9534f;
        }

        .debug {
            text-align: left;
            margin-top: 20px;
            background: #111;
            color: #0f0;
            padding: 15px;
            border-radius: 5px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="box">
        <h1>Something Went Wrong</h1>
        <p>Please try again later or contact support.</p>

        <?php if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true'): ?>
            <div class="debug">
                <strong>Debug Info:</strong><br><br>

                <?php if (!empty($_SESSION['debug_error'])): ?>
                    Type: <?= $_SESSION['debug_error']['type'] ?? '' ?><br>
                    Message: <?= $_SESSION['debug_error']['message'] ?? '' ?><br>
                    File: <?= $_SESSION['debug_error']['file'] ?? '' ?><br>
                    Line: <?= $_SESSION['debug_error']['line'] ?? '' ?><br>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>