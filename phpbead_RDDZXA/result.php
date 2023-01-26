<?php
$id = "";
if(isset($_GET['id'])){
    $id = $_GET['id'];
}

$polls = json_decode(file_get_contents('polls.json'), true);
for($i = 0; $i < count($polls); $i++){
    if($id == $polls[$i]['id']){
        $data = $polls[$i];
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
    <title>Voting session ended</title>
</head>
<body>
    <h2><?= $data['question'] ?></h2>
    
    <?php for($i=0; $i<count($data['answers']); $i++): ?>
        <?php
            $key = array_keys($data['answers'][$i])[0];
            $value = $data['answers'][$i][$key];
        ?>

        <?php echo $key.": ".$value ?>
        <br>
    <?php endfor ?>

    <br>
    <br>
    <h3>Poll's deadline: <?= $data['deadline']?></h3>
    <h3>Poll was created: <?= $data['createdAt']?></h3>
    <br>
    <a href='index.php'>Back to the main page</a>
</body>
</html>