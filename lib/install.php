<?php

/**
 * blog installer function
 * @return array(count array, error string)
 */
function installBlog(PDO $pdo)
{
    // get project paths
    $root = getRoot();
    $database = getDbPath();

    $error = '';

    // A security measure, to avoid anyone resetting the database if it already exists
    if (is_readable($database) && filesize($database) > 0) {
        $error = 'Please delete the existing database manually before installing it afresh';
    }

    // Create an empty file for the database
    if (!$error) {
        $createdOk = @touch($database);
        if (!$createdOk) {
            $error = sprintf(
                'Could not create the database, please allow the server to create new files in \'%s\'',
                dirname($database)
            );
        }
    }

    // Grab the SQL commands we want to run on the database
    if (!$error) {
        $sql = file_get_contents($root . '/data/init.sql');

        if ($sql === false) {
            $error = 'Cannot find SQL file';
        }
    }

    // Connect to the new database and try to run the SQL commands
    if (!$error) {
        $result = $pdo->exec($sql);
        if ($result === false) {
            $error = 'Could not run SQL: ' . print_r($pdo->errorInfo(), true);
        }
    }

    // See how many rows we created, if any
    $count = array();

    foreach (array('post', 'comment') as $tableName) {
        if (!$error) {
            $sql = "SELECT COUNT(*) AS c FROM " . $tableName;
            $stmt = $pdo->query($sql);
            if ($stmt) {
                // We store each count in an associative array
                $count[$tableName] = $stmt->fetchColumn();
            }
        }
    }

    return array($count, $error);
}

/**
 * Updates the admin user in the database
 * @param PDO $pdo
 * @param string $username
 * @param integer $length
 * @return array duple of (password, error)
 */
function createUser(PDO $pdo, $username, $length = 10)
{
    //create a random password
    $alphabet = range(ord('A'), ord('z'));
    $alphabetLength = count($alphabet);

    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $letterCode = $alphabet[rand(0, $alphabetLength - 1)];
        $password .= chr($letterCode);
    }
    $error = '';

    //insert the credentials into the DB
    $sql = "
        UPDATE
            user
        SET
            password = :password, created_at = :created_at, is_enabled = 1
        WHERE
            username = :username
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Could not prepare user update");
    }
    if (!$error) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            $error = "Failed to hash password";
        }
    }
    if (!$error) {
        $result = $stmt->execute(
            array(
                'username' => $username,
                'password' => $hash,
                'created_at' => getRightNowSqlDate(),
            )
        );
        if ($result === false) {
            $error = 'Could not update password';
        }
    }
    if ($error) {
        $password = '';
    }
    return array($password, $error);
}
