<?php

$polls = json_decode(file_get_contents('polls.json'), true);

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
        $id = count($polls) + 1;
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
            'id' => "poll" . $id,
            'question' => $_POST['question'],
            'options' => $options,
            'isMultiple' => $isMultiple,
            'createdAt' => $_POST['created'],
            'deadline' => $_POST['deadline'],
            'answers' => $answers,
            'voted' => []
        ];

        $polls[] = $newPoll;
        file_put_contents('polls.json', json_encode($polls, JSON_PRETTY_PRINT));
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
    <title>Create new POLL</title>
</head>
<body>
    <h1>POLL CREATOR</h1>

    <form action="addpoll.php" method="post" novalidate>
        <label for="question">Question:</label>
        <br>
        <input type="text" name="question" value="<?= $_POST["question"]??"" ?>">
        <br>
        <br>

        <label for="options">Choosable options:</label>
        <br>
        <textarea name="options" type="text">
            <?php if(isset($_POST['options'])) {echo htmlentities($_POST['options']); }?>
        </textarea>
        <br>
        <br>

        <label for="multiple">Can they choose multiple?</label>
        <br>
        
        <input type="radio" name="yes" value="YES" <?= isset($_POST['yes']) ? 'checked' : ''?>>
        <label for="yes">YES</label>
        
        <input type="radio" name="no" value="NO" <?= isset($_POST['no']) ? 'checked' : ''?>>
        <label for="no">NO</label>
        <br>
        <br>

        <label for="deadline">Poll's deadline:</label>
        <br>
        <input type="date" name="deadline" value="<?= $_POST["deadline"]??"" ?>">
        <br>
        <br>

        <input type="hidden" name="created" value="<?= date("Y-m-d") ?>">

        <input type="submit" value="Create Poll">
    </form>


    <?php if (isset($errors) && $errors) : ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Poll creation failed!" ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
        </ul>
    <?php elseif(isset($errors) && !$errors): ?>
        <br>
        <h3>Feedback text</h3>
        <?php echo "Poll creation successfull!" ?>
    <?php endif ?>
    <br>
    <br>
    <br>

    <a href="index.php">Back to the main page</a>
</body>
</html>