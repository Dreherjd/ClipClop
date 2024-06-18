<?php
function addPost(PDO $pdo, $title, $body, $userId)
{
    //insert query
    $sql = "
        INSERT INTO
            post
            (title, body, user_id, created_at)
        VALUES
            (:title, :body, :user_id, :created_at)
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare post insertion query");
    }
    //run the query
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'user_id' => $userId,
            'created_at' => getRightNowSqlDate(),
        )
    );
    if ($result === false) {
        throw new Exception("Could not run post insertion query");
    }
    return $pdo->lastInsertId();
}

function editPost(PDO $pdo, $title, $body, $postId)
{
    //prepare a query
    $sql = "
        UPDATE
            post
        SET
            title = :title,
            body = :body
        WHERE
            id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    //check stmt
    if ($stmt === false) {
        throw new Exception("Could not prepare edit query");
    }
    //run it
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'post_id' => $postId
        )
    );
    if($result === false){
        throw new Exception("Could not execute edit query");
    }
    return true;
}
