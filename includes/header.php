<!DOCTYPE html>
<html>

<head>
    <title>Clip Clop</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link href="styles/bootstrap.css" type="text/css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Clip Clop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <form class="d-flex">
            <?php if (isLoggedIn()) : ?>
                <a href="edit-post.php" class="btn btn-secondary">New Post</a>
                <a href="logout.php" class="btn btn-secondary">Log out</a>
            <?php else : ?>
                <a href="login.php" class="btn btn-secondary">Log in</a>
            <?php endif ?>
        </form>
    </nav>
    <br /><br />