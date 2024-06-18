<?php
require_once 'lib/common.php';
require_once 'lib/edit-post.php';
require_once 'lib/view-post.php';
session_start();
//if user is not logged in, don't let them see this page
if (!isLoggedIn()) {
    redirectAndExit('index.php');
}

//default values
$title = $body = '';
//get database handle
$pdo =  getPDO();

$postId = null;
if (isset($_GET['post_id'])) {
    $post = getPost($pdo, $_GET['post_id']);
    if ($post) {
        $postId = $_GET['post_id'];
        $title = $post['title'];
        $body = $post['body'];
    }
}

//post operations
$errors = array();
if ($_POST) {
    //validate it first
    $title = $_POST['post-title'];
    if (!$title) {
        $errors[] = 'You have to have a title';
    }
    $body = $_POST['post-body'];
    if (!$body) {
        $errors[] = 'You must have a post body';
    }
    if (!$errors) {
        $pdo = getPDO();
        //check if editing or adding
        if ($postId) {
            editPost($pdo, $title, $body, $postId);
        } else {
            $userId = getAuthUserId($pdo);
            $postId = addPost($pdo, $title, $body, $userId);
            if ($postId === false) {
                $errors[] = 'Post operation failed';
            }
        }
    }
    if (!$errors) {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }
}

?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <?php if ($errors) : ?>
        <div class="error box">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>
    <form method="post" class="post-form user-form">
        <div>
            <label for="post-title" class="form-label">Title:</label>
            <input id="post-title" class="form-control" name="post-title" value="<?php echo escapeHTML($title); ?>" type="text" />
        </div>
        <div>
            <label for="post-body" class="form-label">Body:</label>
            <textarea id="post-body" class="form-control" name="post-body" value="<?php echo escapeHTML($body); ?>" rows="12" cols="70"></textarea>
        </div>
        <div>
            <input class="btn btn-primary" type="submit" value="Save post" />
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
</div>
</form>


<?php include 'includes/footer.php'; ?>