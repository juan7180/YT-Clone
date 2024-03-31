<?php
session_start();
require_once "db_connect.php";

$logged_in = isset($_SESSION['user_id']);

$username = "";
if ($logged_in) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM videos ORDER BY id DESC");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YT - Homepage</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            width: 100%;
            max-width: 300px;
            margin-bottom: 20px;
        }
        .card-img-top {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Home</h2>
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
            <div class="container">
                <ul class="navbar-nav mr-auto">
                    <?php if ($logged_in): ?>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="upload">Upload</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <span class="navbar-text mx-auto">
                    <?php if ($logged_in): ?>
                        Welcome, <?php echo $username; ?>!
                    <?php endif; ?>
                </span>
                <ul class="navbar-nav ml-auto">
                    <?php if ($logged_in): ?>
                        <li class="nav-item">
                            <a class="btn btn-danger" href="?logout">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="signup">Sign Up</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-success" href="login">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <hr>
        <div class="row">
            <?php foreach ($videos as $video): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img class="card-img-top" src="<?php echo htmlspecialchars($video['thumbnail_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Thumbnail">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($video['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($video['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="watch?v=<?php echo $video['id']; ?>" class="btn btn-primary">Watch</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
