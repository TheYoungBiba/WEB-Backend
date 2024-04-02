<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="bootstrap.min.css"/>
    <title>Task3</title>
</head>


<?php

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {

        print('Результаты сохранены');
    }
    include('form.php');
    exit();
}

function emptyValue($value, $field, $type = 0)
{
    if (empty($value)) {
        if ($type == 0) {
            printError("Заполните поле $field.<br/>");
        }
        if ($type == 1) {
            printError("Выберите $field.<br/>");
        }
        if ($type == 2) {
            printError("ознакомьтесь с соглашением<br/>");
        }
        exit();
    }
}

function printError($errorMessage)
{
    print("<div class='messageError'>$errorMessage</div>");
    exit();
}

$errors = '';
$name = ($_POST['name'] ?? '');
$number = ($_POST['number'] ?? '');
$email = ($_POST['email'] ?? '');
$data = (isset($_POST['data']) ? strtotime($_POST['data']) : '');
$radio = ($_POST['radio'] ?? '');
$lang = ($_POST['lang'] ?? '');
$biography = ($_POST['biography'] ?? '');
$check_mark = ($_POST['check_mark'] ?? '');
$number = preg_replace('/\D/', '', $number);
$langs = ($lang != '') ? implode(", ", $lang) : [];

emptyValue($name, "имя");
emptyValue($number, "телефон");
emptyValue($email, "email");
emptyValue($data, "дата");
emptyValue($radio, "пол", 1);
emptyValue($lang, "языки", 1);
emptyValue($biography, "биография");
emptyValue($check_mark, "ознакомлен", 2);

if (empty($name)) {
    print('Заполните поле ФИО');
}

if (strlen($name) > 255) {
    $errors = 'Превышение по количеству символов в поле ФИО > 255 символов';
} elseif (count(explode(" ", $name)) < 2) {
    $errors = 'Неверный формат ФИО';
} elseif (strlen($number) != 11) {
    $errors = 'Неверное значение поля Телефон';
} elseif (strlen($email) > 255) {
    $errors = 'Превышение по количеству символов в поле email > 255 символов';
} elseif (!preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email)) {
    $errors = 'Неверное значение поля "email"';
} elseif (!is_numeric($data) || strtotime("now") < $data) {
    $errors = 'Укажите корректную дату';
} elseif ($radio != "m" && $radio != "f") {
    $errors = 'Укажите пол';
} elseif (count($lang) == 0) {
    $errors = 'Укажите языки';
}

if ($errors != '') {
    printError($errors);
}

$db = new PDO('mysql:host=localhost;dbname=u67401', 'u67401', '6728742',
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$inQuery = implode(',', array_fill(0, count($lang), '?'));

try {
    $dbLangs = $db->prepare("SELECT id, name FROM languages WHERE name IN ($inQuery)");
    foreach ($lang as $key => $value) {
        $dbLangs->bindValue(($key + 1), $value);
    }
    $dbLangs->execute();
    $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}

echo $dbLangs->rowCount() . '**' . count($lang);

if ($dbLangs->rowCount() != count($lang)) {
    $errors = 'Неверно выбраны языки';
} elseif (strlen($biography) > 65535) {
    $errors = 'Превышение по количеству символов в поле биография" > 65 535 символов';
}

if ($errors != '') {
    printError($errors);
}

try {
    $stmt = $db->prepare("INSERT INTO users (name, phone, email, birthday, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $number, $email, $data, $radio, $biography]);
    $fid = $db->lastInsertId();
    $stmt1 = $db->prepare("INSERT INTO users_languages (user_id, lang_id) VALUES (?, ?)");
    foreach ($languages as $row) {
        $stmt1->execute([$fid, $row['id']]);
    }
} catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');
