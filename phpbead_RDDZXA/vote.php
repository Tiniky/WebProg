<?php
$userid = isset($_POST['userid']) ? $_POST['userid'] : $_GET['userid'];
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $idAT = "";

    $polls = json_decode(file_get_contents('polls.json'), true);
    for($i = 0; $i < count($polls); $i++){
        if($id == $polls[$i]['id']){
            $data = $polls[$i];
            $idAT = $i;
        }
    }
} elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
    $idAT = "";

    $polls = json_decode(file_get_contents('polls.json'), true);
    for($i = 0; $i < count($polls); $i++){
        if($id == $polls[$i]['id']){
            $data = $polls[$i];
            $idAT = $i;
        }
    }
}

$userid = isset($_POST['userid']) ? $_POST['userid'] : $_GET['userid'];
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

if(count($_POST)>1){
    if($data['isMultiple']){
        for($i = 0; $i < count($polls[$idAT]['answers']); $i++){
            if(isset($_POST["nr".$i])){
                $polls[$idAT]['answers'][$i][array_keys($polls[$idAT]['answers'][$i])[0]]++;
            }
        }
    } else{
        for($i = 0; $i < count($polls[$idAT]['answers']); $i++){
            if(isset($_POST["nr"])){
                $polls[$idAT]['answers'][$i][array_keys($polls[$idAT]['answers'][$i])[0]]++;
            }
        }
    }

    array_push($polls[$idAT]['voted'], $userid);

    file_put_contents('polls.json', json_encode($polls, JSON_PRETTY_PRINT));
    echo "Vote sent!";

} elseif(count($_POST) == 1){
    echo "You have to select on option in order to send vote!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Your opinion matters (I swear)</title>
</head>
<body>
    <h2><?= $data['question'] ?></h2>

    <form action="vote.php" method="post">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="userid" value="<?=$userid?>">
        <?php if($data['isMultiple']): ?>
            <?php for($i = 0; $i < count($data['options']); $i++) : ?>
            <input type="checkbox" name="nr<?=$i?>" value="<?=$data['options'][$i]?>"><?= $data['options'][$i]?>
            <br>
            <?php endfor ?>
        <?php else: ?>
            <?php for($i = 0; $i < count($data['options']); $i++) : ?>
            <input type="radio" name="nr" value="<?=$data['options'][$i]?>"><?= $data['options'][$i]?>
            <br>
            <?php endfor ?>
        <?php endif ?>
        <br>
        <input type="submit" value="SEND">
    </form>
    
    <br>
    <br>
    <h3>Poll's deadline: <?= $data['deadline']?></h3>
    <h3>Poll was created: <?= $data['createdAt']?></h3>
    <br>
    <a href='index.php'>Back to the main page</a>
</body>
</html>