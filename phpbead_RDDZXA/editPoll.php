<?php
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$polls = json_decode(file_get_contents('polls.json'), true);
$newpolls = [];

$poll = "";
for ($i = 0; $i < count($polls); $i++){
    if($id === $polls[$i]['id']){
        $poll = $polls[$i];
    } else{
        $newpolls[] = $polls[$i];
    }
}

if(count($_POST) > 1){   
    $errors = []; 
    if(!isset($_POST['question']) || trim($_POST['question']) === ''){
        $errors[] = 'The question is mandatory!';
    }

    if(!isset($_POST['options']) || trim($_POST['options']) === ''){
        $errors[] = 'Giving options is mandatory!';
    } else{
        foreach(explode("\n", $_POST['options']) as $option){
            if(trim($option) === ''){
                $errors[] = 'Giving options is mandatory!';
            }
        }
    }

    $isMultiple = "";
    if(!isset($_POST['yes']) && !isset($_POST['no'])){
        $errors[] = 'Choosing multiplicity is mandatory!';
    } elseif(isset($_POST['yes'])){
        $isMultiple = true;
    } elseif(isset($_POST['no'])){
        $isMultiple = false;
    }

    if(!isset($_POST['deadline']) || strlen($_POST['deadline']) == 0){
        $errors[] = 'The deadline is mandatory!';
    }

    if(isset($errors) && !$errors){
        $optionsUnchecked = explode("\n", $_POST['options']);
        $options = [];
        $answers = [];

        for($i = 0; $i < count($optionsUnchecked); $i++){
            if(trim($optionsUnchecked[$i]) !== ""){
                $options[] = trim($optionsUnchecked[$i]);
                $answers[] = array($options[$i] => 0);
            }
        }

        $newPoll = [
            'id' => $id,
            'question' => $_POST['question'],
            'options' => $options,
            'isMultiple' => $isMultiple,
            'createdAt' => $_POST['created'],
            'deadline' => $_POST['deadline'],
            'answers' => $answers,
            'voted' => []
        ];

        $newpolls[] = $newPoll;
        file_put_contents('polls.json', json_encode($newpolls, JSON_PRETTY_PRINT));
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
    <title>Edit POLL</title>
</head>
<body>
    <h1>POLL EDITOR</h1>

    <form action="editPoll.php" method="post" novalidate>
        <input type="hidden" name="id" value="<?=$id?>">
        <label for="question">Question:</label>
        <br>
        <input type="text" name="question" value="<?= $_POST["question"]?? $poll['question'] ?>">
        <br>
        <br>

        <label for="options">Choosable options:</label>
        <br>
        <textarea name="options" type="text">
            <?php echo implode("\n", $poll['options']); ?>
        </textarea>
        <br>
        <br>

        <label for="multiple">Can they choose multiple?</label>
        <br>
        
        <input type="radio" name="yes" value="YES" <?= $poll['isMultiple'] ? 'checked' : '' ?>>
        <label for="yes">YES</label>
        
        <input type="radio" name="no" value="NO" <?= !$poll['isMultiple'] ? 'checked' : '' ?>>
        <label for="no">NO</label>
        <br>
        <br>

        <label for="deadline">Poll's deadline:</label>
        <br>
        <input type="date" name="deadline" value="<?= $_POST["deadline"]?? $poll['deadline'] ?>">
        <br>
        <br>

        <label for="created">Poll's creation date:</label>
        <br>
        <input type="date" name="created" value="<?= $poll['createdAt'] ?>">
        <br>
        <br>

        <input type="submit" value="Edit Poll">
    </form>


    <?php if (isset($errors) && $errors) : ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Poll edit failed!" ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
        </ul>
    <?php elseif(isset($errors) && !$errors): ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Poll edit successfull!" ?>
    <?php endif ?>
    <br>
    <br>
    <br>

    <a href="index.php">Back to the main page</a>
</body>
</html>