<?php
// Work out the path to the database, so SQLite/PDO can connect
require_once 'lib/common.php';

session_start();

$pdo = getPDO();
$stmt = $pdo->query(
    'SELECT
        id, title, created_at, body
    FROM
        post
    ORDER BY
        created_at DESC'
);
if ($stmt === false) {
    throw new Exception('There was a problem running this query');
}
$result = $stmt->execute();
if ($result === null) {
    throw new Exception("There was an issue running the query");
}
$post = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notFound = isset($_GET['not-found']);

?>
<?php include("includes/header.php") ?>
<div class="container">

    <?php if ($notFound) : ?>
        <div style="border: 1px solid #ff6666; padding: 6px">
            The post you've requested could not be found
        </div>
    <?php endif ?>

    <div class="row">
        <?php foreach ($post as $post) : ?>
            <div class="col-4">
                <a href="view-post.php?post_id=<?php echo $post['id'] ?>">
                    <div class="card text-white bg-primary mb-3 h-100" style="max-width: 20rem;">
                        <h4 class="card-header"><?php echo escapeHTML($post['title']) ?></h4>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo convertSqlDate($post['created_at']) ?></h5>
                            <p class="card-text"><?php echo convertNewLinesToParagraphs($post['body']) ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include("includes/footer.php") ?>