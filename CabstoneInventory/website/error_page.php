<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - CABSTONE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fdfaf7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .error-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            margin: 20px;
        }
        .error-icon {
            font-size: 3rem;
            color: #ef4444; /* Red-500 */
            margin-bottom: 20px;
        }
        .error-heading {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937; /* Gray-800 */
            margin-bottom: 10px;
        }
        .error-message {
            font-size: 1rem;
            color: #4b5563; /* Gray-600 */
            margin-bottom: 30px;
        }
        .home-button {
            background-color: #3b82f6; /* Blue-500 */
            color: white;
            padding: 10px 20px;
            border-radius: 9999px; /* Full rounded */
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .home-button:hover {
            background-color: #2563eb; /* Blue-600 */
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">&#9888;</div> <h1 class="error-heading">Oops! Something went wrong.</h1>
        <p class="error-message">
            <?php
            // Get the error type from the URL query parameter
            $error_type = $_GET['type'] ?? 'unknown'; // Defaults to 'unknown' if no type is provided

            // Display a specific message based on the error type
            switch ($error_type) {
                case 'db_connection':
                    echo "We're having trouble connecting to our database. Please try again later.";
                    break;
                case 'invalid_product':
                    echo "The product you're looking for could not be found. It might have been removed or the link is incorrect.";
                    break;
                default:
                    echo "An unexpected error occurred. We're working to fix it!";
                    break;
            }
            ?>
        </p>
        <a href="cabstone_site.php" class="home-button">Go to Homepage</a>
    </div>
</body>
</html>