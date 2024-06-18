<?php
require_once 'lib/common.php';
require_once 'lib/view-post.php';

session_start();

//get the id from the url
if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
} else {
    //set a default
    $postId = 0;
}

$pdo = getPDO();
$row = getPost($pdo, $postId);

//check if post exists
if (!$row) {
    redirectAndExit('index.php?not-found=1');
}

$errors = null;
if ($_POST) {
    $commentData = array(
        'name' => $_POST['comment-name'],
        'website' => $_POST['comment-website'],
        'text' => $_POST['comment-text'],
    );
    $errors = addComment(
        $pdo,
        $postId,
        $commentData
    );
    //if there are no errors, redirect and redisplay the post
    if (!$errors) {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }
} else {
    $commentData = array(
        'name' => '',
        'website' => '',
        'text' => '',
    );
}

//swap carriage returns for paragraph breaks
$bodyText = escapeHTML($row['body']);
$paraText = str_replace("\n", "<p></p>", $bodyText);

?>

<?php include("includes/header.php") ?>

<div class="container">
    <div class="card text-white bg-primary mb-3" style="max-width: 60rem;">
        <div class="card-header">
            <h1><?php echo escapeHTML($row['title']) ?></h1>
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo convertSqlDate($row['created_at']) ?></h5>
            <p class="card-text"><?php echo $paraText ?></p>
        </div>
    </div>

    <h4>Comments</h4>
    <?php foreach (getCommentsByPost($pdo, $postId) as $comment) : ?>
        <?php // For now, we'll use a horizontal rule-off to split it up a bit 
        ?>
        <hr />
        <div class="comment">
            <div class="comment-meta">
                <i><?php echo escapeHTML($comment['name']) ?></i>
                -
                <?php echo convertSqlDate($comment['created_at']) ?>
            </div>
            <div class="comment-body">
                <?php // This is already escaped 
                ?>
                <h5><?php echo convertNewlinesToParagraphs($comment['text']) ?></h5>
            </div>
        </div>
    <?php endforeach ?>
    <?php require 'templates/comment-form.php' ?>
</div>
<?php include("includes/footer.php") ?>