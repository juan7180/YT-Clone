<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YT - Watch</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS styles */
        .video-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        .video-info {
            margin-top: 20px;
        }
        .video-info h2 {
            margin-top: 0;
        }
        .back-to-home {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Include db_connect.php to establish database connection
    require_once "db_connect.php";

    if (isset($_GET['v'])) {
        try {
            // Fetch video details from the database, including the username of the uploader
            $stmt = $pdo->prepare("SELECT v.*, u.username FROM videos v JOIN users u ON v.user_id = u.id WHERE v.id = ?");
            $stmt->execute([$_GET['v']]);
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($video) {
                // Escape HTML entities to prevent XSS
                $title = htmlspecialchars($video['title']);
                $description = htmlspecialchars($video['description']);
                $username = htmlspecialchars($video['username']);

                // Display video details
                echo "<div class='video-container'>";
                echo "<video controls width='640' height='360'>";
                echo "<source src='{$video['file_path']}' type='video/mp4'>";
                echo "Your browser does not support the video tag.";
                echo "</video>";

                echo "<div class='video-info'>";
                echo "<h2>{$title}</h2>";
                echo "<p>Description: {$description}</p>";
                echo "<p>Uploaded by: {$username}</p>"; // Display the username of the uploader
                echo "</div>";

                echo "</div>";

                // Back to home button
                echo "<a class='btn btn-primary back-to-home' href='index.php'>Back to Home</a>";
            } else {
                // Video not found
                echo "<div class='alert alert-danger' role='alert'>Video not found.</div>";
            }
        } catch (PDOException $e) {
            // Database error
            echo "<div class='alert alert-danger' role='alert'>Database error: " . $e->getMessage() . "</div>";
        }
    } else {
        // Video ID not provided in URL
        echo "<div class='alert alert-warning' role='alert'>Video ID not provided.</div>";
    }
    ?>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
