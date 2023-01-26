<?php
if(isset($_GET['logout']) && $_GET['logout'] == true){
    $_SESSION = [];
}

if(isset($_GET['name']) && isset($_GET['id']) && isset($_GET['admin'])){
    $loggedIn = true;
    session_start();
    $_SESSION['name'] = $_GET['name'];
    $_SESSION['id'] = $_GET['id'];
    $_SESSION['isAdmin'] = $_GET['admin'];

    echo "Login successfull! Welcome " . $_SESSION['name'] . "! :D";
}

$onGoing = [];
$alreadyExpired = [];
$onGoingDB = 1;
$alreadyExpiredDB = 1;

$polls = json_decode(file_get_contents('polls.json'), true);
for($i = 0; $i<count($polls); $i++){
    if(strtotime($polls[$i]['deadline']) > strtotime(date("Y-m-d"))){
        $onGoing[] = $polls[$i];
    } else{
        $alreadyExpired[] = $polls[$i];
    }
}

usort($onGoing, function ($a, $b) {
    return $b['createdAt'] <=> $a['createdAt'];
});
usort($alreadyExpired, function ($a, $b) {
    return $b['createdAt'] <=> $a['createdAt'];
});

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Vote (or not)</title>
</head>
<body>
    <h2>Welcome to <i>Vote (or not)</i>, where you can do almost anything like vote or not. Your opinion matters.</h2>
    <h5>We will definitely NOT use your answers against you.</h5>
    <br>

    <table>
        <tr>
            <th>SORSZÁM</th>
            <th>LÉTREJÖTT</th>
            <th>HATÁRIDŐ</th>
            <th>VOTE</th>
        </tr>

        <?php foreach($onGoing as $poll): ?>
            <tr>
                <td><?= $onGoingDB++ ?></td>
                <td><?= $poll['createdAt'] ?></td>
                <td><?= $poll['deadline'] ?></td>
                <td>
                    <form action="<?= (isset($loggedIn) && $loggedIn) ? 'vote.php?userid='.urldecode($_SESSION['id']).'&id='.urlencode($poll['id']) : 'login.php' ?>" method="post">
                        <?php if(isset($loggedIn) && $loggedIn && in_array($_SESSION['id'], $poll['voted'])):?> 
                            <input type="submit" value="EDIT">
                        <?php else: ?>
                            <input type="submit" value="VOTE">
                        <?php endif ?>
                    </form>
                </td>
                <?php if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                    <td>
                        <form action="<?= 'editPoll.php?id='.urlencode($poll['id'])?>" method="post">
                            <input type="submit" value="EDIT POLL">
                        </form>
                    </td>
                    <td>
                        <form action="<?= 'deletePoll.php?id='.urlencode($poll['id'])?>" method="post">
                            <input type="submit" value="DELETE">
                        </form>
                    </td>
                <?php endif ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <table>
        <tr>
            <th>SORSZÁM</th>
            <th>LÉTREJÖTT</th>
            <th>HATÁRIDŐ</th>
            <th>EREDMÉNYEK</th>
        </tr>

        <?php foreach($alreadyExpired as $poll): ?>
            <tr>
                <td><?= $alreadyExpiredDB++ ?></td>
                <td><?= $poll['createdAt'] ?></td>
                <td><?= $poll['deadline'] ?></td>
                <td>
                    <form action="<?= (isset($loggedIn) && $loggedIn) ? 'result.php?id='.urlencode($poll['id']) : 'login.php' ?>" method="post">
                        <input type="submit" value="RESULT">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <br>
    <br>
    <br>
    <?php if(isset($loggedIn) && $loggedIn && $_SESSION['isAdmin']): ?>
    <a href='addpoll.php'>ADD POLL</a>
    <br>
    <a href='index.php?logout=true'>LOGOUT</a>

    <?php elseif(isset($loggedIn) && $loggedIn): ?>
    <a href='index.php?logout=true'>LOGOUT</a>
    
    <?php else: ?>
    <a href='login.php'>LOGIN</a>
    <br>
    <a href='register.php'>REGISTER</a>
    <?php endif ?>
</body>
</html>