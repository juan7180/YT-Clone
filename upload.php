<?php
require_once "db_connect.php";

$allowedVideoExtensions = ['mp4', 'mov', 'avi'];
$allowedThumbnailExtensions = ['jpg', 'jpeg', 'png'];

// Check if user is logged in and get user ID from session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video']) && isset($_FILES['thumbnail'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate title and description
    if (empty($title)) {
        echo "Title is required";
        exit;
    }

    // Validate video file
    $videoFile = $_FILES['video'];
    $videoExtension = strtolower(pathinfo($videoFile['name'], PATHINFO_EXTENSION));
    if (!in_array($videoExtension, $allowedVideoExtensions)) {
        echo "Only MP4, AVI, and MOV files are allowed for videos";
        exit;
    }

    // Validate thumbnail file
    $thumbnailFile = $_FILES['thumbnail'];
    $thumbnailExtension = strtolower(pathinfo($thumbnailFile['name'], PATHINFO_EXTENSION));
    if (!in_array($thumbnailExtension, $allowedThumbnailExtensions)) {
        echo "Only JPG, JPEG, and PNG files are allowed for thumbnails";
        exit;
    }

    // Move uploaded files to designated directories
    $videoUploadDirectory = 'videos/';
    $thumbnailUploadDirectory = 'thumbnails/';
    
    $videoFilename = uniqid() . '.' . $videoExtension;
    $thumbnailFilename = uniqid() . '.' . $thumbnailExtension;

    $videoUploadPath = $videoUploadDirectory . $videoFilename;
    $thumbnailUploadPath = $thumbnailUploadDirectory . $thumbnailFilename;

    if (move_uploaded_file($videoFile['tmp_name'], $videoUploadPath) && 
        move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailUploadPath)) {
        
        // Insert video info into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO videos (title, description, file_path, thumbnail_path, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $videoUploadPath, $thumbnailUploadPath, $user_id]);
            // Redirect to homepage or show success message
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    } else {
        echo "Error uploading files";
        exit;
    }
}

// HTML form for uploading video and thumbnail
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Upload Video</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="video">Video File</label>
                <input type="file" class="form-control-file" id="video" name="video" accept=".mp4,.avi,.mov" required>
            </div>
            <div class="form-group">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
