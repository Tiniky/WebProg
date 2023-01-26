<?php
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$polls = json_decode(file_get_contents('polls.json'), true);
$newpolls = [];

if(isset($_POST) && $_POST){
    for ($i = 0; $i < count($polls); $i++){
        if($id === $polls[$i]['id']){
            $poll = $polls[$i];
        } else{
            $newpolls[] = $polls[$i];
        }
    }
    
    file_put_contents('polls.json', json_encode($newpolls, JSON_PRETTY_PRINT));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Delete POLL</title>
</head>
<body>
    <h1>DELETE POLL</h1>

    <p>You are about to delete a poll!!<p>

    <form action="deletePoll.php" method="post" novalidate>
    <input type="hidden" name="id" value="<?=$id?>">
        <label for="confirm">If you are sure about deleting it click the button below!</label>
        <br>
        <br>

        <input type="submit" value="DELETE Poll">
    </form>

    <?php if (isset($_POST) && $_POST) : ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Poll delete successfull!" ?>
    <?php endif ?>
    <br>
    <br>
    <br>

    <a href="index.php">Back to the main page</a>

</body>
</html>