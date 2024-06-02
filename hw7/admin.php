<?php
$db = new PDO(
    'mysql:host=localhost;dbname=u67401',
    'u67401',
    '6728742',
    [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);

$admin = 0;
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $q = $db->prepare("SELECT id from verified_users WHERE role = 'admin' and login = ? and password = ?");
    $q->execute([$_SERVER['PHP_AUTH_USER'], md5($_SERVER['PHP_AUTH_PW'])]);
    $admin = $q->rowCount();
}

if (!$admin) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print ('<h1>401 Требуется авторизация</h1>');
    exit();
}

print ('Вы успешно авторизовались.');

session_start();

if (count($_POST)) {
    $keyPost = checkinput(key($_POST));
    if (empty($_SESSION['rem_but']) || $_SESSION['rem_but'] != $keyPost) {
        $id = explode('-', $keyPost)[1];

        if (!preg_match('/^[0-9]+$/', $id))
            exit("Введите id");

        $dbf = $db->prepare("SELECT * FROM users WHERE id = ?");
        $dbf->execute([$id]);
        if ($dbf->rowCount() != 0) {
            $dels = $db->prepare("DELETE FROM users WHERE id = ?");
            $dels->execute([$id]);
            $dels = $db->prepare("DELETE FROM users_languages WHERE user_id = ?");
            if (!$dels->execute([$id]))
                exit("Ошибка удаления");
        } else
            exit("Форма не найдена");

        $_SESSION['rem_but'] = $keyPost;
    }
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
    <title>Task 6</title>
</head>

<body>
<form method="post" action="">
    <table class="table1">
        <thead>
        <tr>
            <th>id</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Почта</th>
            <th>Дата рождения</th>
            <th>Пол</th>
            <th>Биография</th>
            <th>ЯП</th>
            <th>Редактирование</th>
            <th>Удаление</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $dbFD = $db->query("SELECT * FROM users ORDER BY id DESC");
        while ($row = $dbFD->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr data-id=' . $row['id'] . '>
                  <td>' . $row['id'] . '</td>
                  <td>' . $row['name'] . '</td>
                  <td>' . $row['phone'] . '</td>
                  <td>' . $row['email'] . '</td>
                  <td>' . date('d.m.Y', $row['birthday']) . '</td>
                  <td>' . (($row['gender'] == "male") ? "Мужской" : "Женский") . '</td>
                  <td>' . $row['bio'] . '</td>
                  <td>';
            $dbl = $db->prepare("SELECT * FROM users_languages fd JOIN languages l ON l.id = fd.lang_id WHERE user_id = ?");
            $dbl->execute([$row['id']]);
            while ($row1 = $dbl->fetch(PDO::FETCH_ASSOC))
                echo $row1['name'] . '<br>';
            echo '</td>
                <td><a href="index.php?uid=' . $row['id'] . '" target="_blank">Редактировать</a></td>
                <td><button name="butt-' . $row['id'] . '" class="remove">Удалить</button></td>
              </tr>';
        }
        ?>
        </tbody>
    </table>
</form>


<table class="table2">
    <tr>
        <td>Язык программирования</td>
        <td>Количество пользователей</td>
    </tr>
    <tbody>
    <?php
    $q = $db->query("SELECT l.id, l.name, count(fd.user_id) as count FROM languages l  LEFT JOIN users_languages fd ON fd.lang_id = l.id GROUP by l.id");
    while ($row = $q->fetch(PDO::FETCH_ASSOC))
        echo '<tr>
          <td>' . $row['name'] . '</td>
          <td>' . $row['count'] . '</td>';
    ?>
    </tbody>
</table>
</body>

</html>