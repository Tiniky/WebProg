<?php

if(isset($_GET['name']) && isset($_GET['pw'])){
    $name = $_GET['name'];
    $pw = $_GET['pw'];
    echo "Registration successfull!";
    $registered = true;
} else{
    $registered = false;
}

$errorDB = "";

if (count($_POST) > 0) {
    $errors = [];

    $users = json_decode(file_get_contents('users.json'), true);

    $matchingName = false;
    $matchingPassword = false;
    $isAdmin = false;
    $userID = "";

    if(!isset($_POST['pw']) || trim($_POST['pw']) === ''){
        $errors[] = 'The password is mandatory!';
        
    }

    if(!isset($_POST['name']) || trim($_POST['name']) === ''){
        $errors[] = 'The username is mandatory!';
    } elseif (strlen(trim($_POST['name'])) != 0 && strlen(trim($_POST['pw'])) != 0) {
        for ($i = 0; $i < count($users); $i++) {
            if ($_POST['name'] == $users[$i]['username']) {
                $matchingName = true;

                if ($_POST['pw'] == $users[$i]['password']) {
                    $matchingPassword = true;
                    $userID = $users[$i]['id'];
                    $isAdmin = $users[$i]['isAdmin'];
                } else {
                    break;
                }
            }
        }

        if (!$matchingName) {
            $errors[] = "There is no registered account with that user name! Try registering.";
        }
    
        if ($matchingName && !$matchingPassword) {
            $errors[] = "Wrong password!";
        }
    }

    if (!$errors) {
        $errorDB = 0;
    } else {
        $errorDB = count($errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <h1>LOGIN</h1>
    <form action="login.php" method="post" novalidate>
        <label for="username">Name:</label>
        <br>
        <input type="text" name="name" value="<?= $name ??"" ?>">
        <br>
        <label for="userpw">Password:</label>
        <br>
        <input type="password" name="pw" value="<?= $pw ??"" ?>">
        <br>
        <br>
        <input type="submit" value="Logging in">
    </form>

    <?php if ($errorDB > 0) : ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Login failed!" ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
        </ul>
    <?php elseif($errorDB == 0): ?>
        <?php header("Location:index.php?name=".urlencode($_POST['name'])."&id=".urlencode($userID)."&admin=".urlencode($isAdmin)); ?>
    <?php endif ?>

    <br>
    <br>
    <?php if (!$registered) : ?>
        <a href='register.php'>REGISTER</a>
    <?php endif ?>
    <br>
    <a href='index.php'>Back to the main page</a>
</body>
</html>