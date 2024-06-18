<?php

/**
 * gets a single post
 * @param PDO $pdo
 * @param integer $postId
 * @throw Exception
 * @return $row
 */
function getPost(PDO $pdo, $postId)
{
    //write the query
    $stmt = $pdo->prepare(
        "SELECT
            title, created_at, body
        FROM
            post
        WHERE
            id = :id    "
    );
    //check for errors in preparing the query
    if ($stmt === false) {
        throw new Exception("There was an issue preparing the query");
    }
    //execute the query
    $result = $stmt->execute(
        array('id' => $postId,)
    );
    //check for errors in execution
    if ($result === false) {
        throw new Exception("There was an issue executing the query");
    }
    //actually get the row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}

/**
 * Post a comment
 * 
 * @param PDO $pdo
 * @param integer $postId
 * @param array $commentData
 * @return array
 */
function addComment(PDO $pdo, $postId, array $commentData)
{
    $errors = array();
    //validation
    if (empty($commentData['name'])) {
        $errors['name'] = "A name is required";
    }
    if (empty($commentData['text'])) {
        $errors['text'] = "A comment is required";
    }
    //if no errors, write the comment
    //prepare the query
    if (!$errors) {
        $sql = "
            INSERT INTO
                comment
            (name, website, text, created_at, post_id)
            VALUES(:name, :website, :text, :created_at, :post_id)
        ";
        $stmt = $pdo->prepare($sql);
        //stop if there's an error
        if ($stmt === false) {
            throw new Exception("There was an issue preparing the query");
        }

        $result = $stmt->execute(
            array_merge(
                $commentData,
                array('post_id' => $postId, 'created_at' => getRightNowSqlDate(), )
            )
        );
        if ($result === false) {
            $errorInfo = $stmt->errorInfo();
            if ($errorInfo) {
                $errors[] = $errorInfo[2];
            }
        }
    }
}
