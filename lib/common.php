<?php

/**
 * get the root of the project
 * @return string
 */
function getRoot()
{
    return realpath(__DIR__ . '/..');
}

/**
 * get the full path for database
 * @return string
 */
function getDbPath()
{
    return getRoot() . '/data/data.sqlite';
}

/**
 * Get the DSN for the SQLite connection.
 * @return string
 */
function getDsn()
{
    return 'sqlite:' . getDbPath();
}

/**
 * gets the PDO object for db access
 * @return \PDO
 */
function getPDO()
{
    $pdo = new PDO(getDSN());
    $result = $pdo->query('PRAGMA foreign_keys = on');
    if ($result === false) {
        throw new Exception("could not turn on foreign keys");
    }
    return $pdo;
}

/**
 * Makes HTML safe to output
 * @param string $html
 * @return  string
 */
function escapeHTML($html)
{
    return htmlspecialchars($html, ENT_HTML5, 'UTF-8');
}

function convertSqlDate($sqlDate)
{
    /* @var $date DateTime */
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $sqlDate);

    return $date->format('d M Y, H:i');
}

function getRightNowSqlDate()
{
    return date('Y-m-d H:i:s');
}

/**
 * converts unsafe text to safe, paragraphed, HTML
 * @param string $text
 * @return string
 */
function convertNewLinesToParagraphs($text)
{
    $escaped = escapeHTML($text);
    return '<p>' . str_replace("\n", "</p><p>", $escaped) . '</p>';
}

/**
 * show a message and redirect user if the try to get at a post that does not exist
 */
function redirectAndExit($script)
{
    //get the domain-relative URL out of the folder structure
    $relativeUrl = $_SERVER['PHP_SELF'];
    $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);

    //redirect to the full url
    $host = $_SERVER['HTTP_HOST'];
    $fullUrl = 'http://' . $host . $urlFolder . $script;
    header('Location: ' . $fullUrl);
    exit();
}

/**
 * returns the number of comments for specified post
 * @param Integer $postId
 * @return Integer
 */
function countCommentsForPost(PDO $pdo, $postId)
{
    $sql = "
        SELECT
            count(*) c
        FROM
            comment
        WHERE
            post_id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array('post_id' => $postId,)
    );
    return (int) $stmt->fetchColumn();
}

/**
 * returns all comments for specified post
 * @param Integer $postId
 */
function getCommentsByPost(PDO $pdo, $postId)
{
    $sql = "
        SELECT
            id, name, text, created_at, website
        FROM
            comment
        WHERE
            post_id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array('post_id' => $postId,)
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function tryLogin(PDO $pdo, $username, $password)
{
    $sql = "
        SELECT
            password
        FROM
            user
        WHERE
            username = :username
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array('username' => $username,)
    );
    //get the hash from the row, use a hash library to verify
    $hash = $stmt->fetchColumn();
    $success = password_verify($password, $hash);
    var_dump($success);
    return true;
}

/**
 * log the user in
 */
function login($username)
{
    session_regenerate_id();
    $_SESSION['logged_in_username'] = $username;
}

/**
 * check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['logged_in_username']);
}

/**
 * Logs out the user
 */
function logout()
{
    unset($_SESSION['logged_in_user']);
}

function getAuthUser()
{
    return isLoggedIn() ? $_SESSION['logged_in_username'] : null;
}

/**
 * Checks the user id for against current authed user
 */
function getAuthUserId(PDO $pdo)
{
    //if no logged in user, stop
    if (!isLoggedIn()) {
        return null;
    }
    $sql = "
        SELECT
            id
        FROM
            user
        WHERE
            username = :username
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array(
            'username' => getAuthUser()
        )
    );
    return $stmt->fetchColumn();
}
