<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "db_connect.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["video"]) && isset($_FILES["thumbnail"])) {
    $title = htmlspecialchars(trim($_POST["title"]), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST["description"]), ENT_QUOTES, 'UTF-8');
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if ($_FILES["video"]["error"] == 4 || $_FILES["thumbnail"]["error"] == 4) {
        $errors[] = "Please select both a video and a thumbnail";
    } else {
        $video_name = $_FILES["video"]["name"];
        $video_tmp_name = $_FILES["video"]["tmp_name"];
        $video_size = $_FILES["video"]["size"];

        $thumbnail_name = $_FILES["thumbnail"]["name"];
        $thumbnail_tmp_name = $_FILES["thumbnail"]["tmp_name"];

        // Validate file types
        $allowed_video_extensions = array("mp4", "avi", "mov");
        $video_file_extension = pathinfo($video_name, PATHINFO_EXTENSION);
        if (!in_array(strtolower($video_file_extension), $allowed_video_extensions)) {
            $errors[] = "Only MP4, AVI, and MOV files are allowed for videos";
        }

        $allowed_image_extensions = array("jpg", "jpeg", "png");
        $thumbnail_file_extension = pathinfo($thumbnail_name, PATHINFO_EXTENSION);
        if (!in_array(strtolower($thumbnail_file_extension), $allowed_image_extensions)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed for thumbnails";
        }

        // Validate file sizes (in bytes)
        $max_video_size = 734003200; // 100MB
        if ($video_size > $max_video_size) {
            $errors[] = "Video file size exceeds maximum limit (100MB)";
        }

        // If no errors, move the files to the uploads and thumbnails directories
        if (empty($errors)) {
            $uploads_dir = "videos/";
            $thumbnails_dir = "thumbnails/";

            $video_path = $uploads_dir . uniqid() . '_' . $video_name;
            $thumbnail_path = $thumbnails_dir . uniqid() . '_' . $thumbnail_name;

            move_uploaded_file($video_tmp_name, $video_path);
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path);

            // Store information in the database
            $stmt = $pdo->prepare("INSERT INTO videos (title, description, file_path, thumbnail_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $video_path, $thumbnail_path]);

            // Redirect to homepage or show success message
            header("Location: index.php");
            exit();
        }
    }
}

// If the code reaches this point, there were errors
// You might want to handle errors by displaying them or logging them
// For simplicity, I'm redirecting back to the upload page with an error query parameter
header("Location: upload?error=true");
exit();
?>
