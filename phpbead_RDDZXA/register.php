<?php
$users = json_decode(file_get_contents('users.json'), true);
$errorDB = "";

if($_POST){
    $errors = [];
    if(!isset($_POST['name']) || trim($_POST['name']) === ''){
        $errors[] = 'The username is mandatory!';
    } elseif(strlen(trim($_POST['name'])) < 5){
        $errors[] = 'The username is too short.';
    } else{
        for ($i = 0; $i < count($users); $i++){
            if(trim($_POST['name']) == $users[$i]['username']){
                $errors[] = 'That username is taken.';
            }
        }
    }

    if(!isset($_POST['email']) || trim($_POST['email']) === ''){
        $errors[] = 'The email address is mandatory!';
    } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors[] = 'Wrong format! Check your e-mail address.';
    } else{
        for ($i = 0; $i < count($users); $i++){
            if(trim($_POST['email']) == $users[$i]['email']){
                $errors[] = 'That e-mail address already has a registered account! Try logging in.';
            }
        }
    }

    if(!isset($_POST['pw']) || !isset($_POST['pw_conf'])){
        $errors[] = 'The password is mandatory!';
    } elseif(strlen($_POST['pw']) < 5 || strlen($_POST['pw_conf']) < 5){
        $errors[] = 'The password is too short.';
    } elseif($_POST['pw'] != $_POST['pw_conf']){
        $errors[] = 'The passwords are not matching!';
    }

    if(!$errors){
        $errorDB = 0;
        $id = count($users) + 1;

        $newUser = [
            'id' => "userid" . $id,
            'username' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $_POST['pw'],
            'isAdmin' => false
        ];

        $users[] = $newUser;
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    } else{
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
    <title>Register</title>
</head>
<body>
    <h1>REGISTER</h1>

    <form action="register.php" method="post" novalidate>
        <label for="username">Name:</label>
        <br>
        <input type="text" name="name" value="<?= $_POST["name"]??"" ?>">
        <br>
        <label for="useremail">E-mail:</label>
        <br>
        <input type="text" name="email" value="<?= $_POST["email"]??"" ?>">
        <br>
        <label for="userpw">Password:</label>
        <br>
        <input type="password" name="pw">
        <br>
        <label for="userpw">Confirm password:</label>
        <br>
        <input type="password" name="pw_conf">
        <br>
        <br>
        <input type="submit" value="Register">
    </form>

    <?php if ($errorDB > 0) : ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Registration failed!" ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
        </ul>
    <?php elseif($errorDB == 0): ?>
        <?php header("Location:login.php?name=".urlencode($_POST['name'])."&pw=".urlencode($_POST['pw'])); ?>
    <?php endif ?>

    <br>
    <br>
    <br>
    <a href='index.php'>Back to the main page</a>
</body>
</html>