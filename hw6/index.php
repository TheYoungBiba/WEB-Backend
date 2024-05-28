<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

$log = !empty($_SESSION['login']);

$db = new PDO(
    'mysql:host=localhost;dbname=u67401',
    'u67401',
    '6728742',
    [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);

$adminLog = isset($_SERVER['PHP_AUTH_USER']);
$uid = isset($_SESSION['id']) ? $_SESSION['id'] : '';
$getUid = isset($_GET['uid']) ? strip_tags($_GET['uid']) : '';

if ($adminLog && preg_match('/^[0-9]+$/', $getUid)) {
    $uid = $getUid;
    $log = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (($adminLog && !empty($getUid)) || !$adminLog) {
        $cookAdmin = (!empty($_COOKIE['admin_value']) ? $_COOKIE['admin_value'] : '');
        if ($cookAdmin == '1') {
            setcookie('fio_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('number_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('email_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('date_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('radio_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('language_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('bio_value', '', time() - 30 * 24 * 60 * 60);
            setcookie('check_value', '', time() - 30 * 24 * 60 * 60);
        }
    }
    $name = (!empty($_COOKIE['name_err']) ? $_COOKIE['name_err'] : '');
    $phone = (!empty($_COOKIE['phone_err']) ? $_COOKIE['phone_err'] : '');
    $email = (!empty($_COOKIE['email_err']) ? $_COOKIE['email_err'] : '');
    $birthday = (!empty($_COOKIE['birthday_err']) ? $_COOKIE['birthday_err'] : '');
    $gender = (!empty($_COOKIE['gender_err']) ? $_COOKIE['gender_err'] : '');
    $lang = (!empty($_COOKIE['lang_err']) ? $_COOKIE['lang_err'] : '');
    $bio = (!empty($_COOKIE['bio_err']) ? $_COOKIE['bio_err'] : '');
    $check_mark = (!empty($_COOKIE['check_mark_err']) ? $_COOKIE['check_mark_err'] : '');
    $errs = array();
    $mess = array();
    $vals = array();
    $err = true;

    function emptyValue($enName, $val) {
        global $errs, $vals, $mess, $err;
        if($err) {
            $err = empty($_COOKIE[$enName.'_err']);
        }
        $errs[$enName] = !empty($_COOKIE[$enName . '_err']);
        $mess[$enName] = "<div class='messageError'>$val</div>";
        $vals[$enName] = empty($_COOKIE[$enName . '_val']) ? '' : $_COOKIE[$enName . '_val'];
        deleteCookies($enName);
    }

    function setValue($enName, $param){
        global $values;
        $values[$enName] = empty($param) ? '' : strip_tags($param);
    }

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('password', '', 100000);
        $mess['success'] = '<div class="message">сохранено</div>';
        if (!empty($_COOKIE['password'])) {
            $mess['info'] = sprintf(
                'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['password'])
            );
        }
    }

    emptyValue('name', $name);
    emptyValue('phone', $phone);
    emptyValue('email', $email);
    emptyValue('birthday', $birthday);
    emptyValue('gender', $gender);
    emptyValue('lang', $lang);
    emptyValue('bio', $bio);
    emptyValue('check_mark', $check_mark);

    $langs = explode(',', $vals['lang']);

    if ($err && !empty($_SESSION['login'])) {
        try {
            $dbFD = $db -> prepare("SELECT * FROM users WHERE id = ?");
            $dbFD -> execute([$_SESSION['id']]);
            $fet = $dbFD -> fetchAll(PDO::FETCH_ASSOC)[0];
            $user_id = $fet['id'];
            $_SESSION['id'] = $user_id;
            $dbL = $db->prepare("SELECT l.name FROM users_languages f LEFT JOIN languages l ON l.id = f.lang_id WHERE f.user_id = ?");
            $dbL->execute([$user_id]);
            $langs = [];
            foreach($dbL->fetchAll(PDO::FETCH_ASSOC) as $item){
                $langs[] = $item['name'];
            }
            setValue('name', $fet['name']);
            setValue('phone', $fet['phone']);
            setValue('email', $fet['email']);
            setValue('birthday', date("Y-m-d", $fet['birthday']));
            setValue('gender', $fet['gender']);
            setValue('lang', $lang);
            setValue('bio', $fet['bio']);
            setValue('check_mark', $fet['check_mark']);

        } catch(PDOException $e) {
            print('Error : ' . $e -> getMessage());
            exit();
        }
    }

    include('form.php');
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = (!empty($_POST['name']) ? $_POST['name'] : '');
    $phone = (!empty($_POST['phone']) ? $_POST['phone'] : '');
    $email = (!empty($_POST['email']) ? $_POST['email'] : '');
    $birthday = (!empty($_POST['birthday']) ? $_POST['birthday'] : '');
    $gender = (!empty($_POST['gender']) ? $_POST['gender'] : '');
    $lang = (!empty($_POST['lang']) ? $_POST['lang'] : '[]');
    $bio = (!empty($_POST['bio']) ? $_POST['bio'] : '');
    $check_mark = (!empty($_POST['check_mark']) ? $_POST['check_mark'] : '');

    if (isset($_POST['logout_form'])) {
        if ($adminLog && empty($_SESSION['login']))
            header('Location: admin.php');
        else {
            deleteCookies('name', 1);
            deleteCookies('phone', 1);
            deleteCookies('email', 1);
            deleteCookies('birthday', 1);
            deleteCookies('gender', 1);
            deleteCookies('lang', 1);
            deleteCookies('bio', 1);
            deleteCookies('check_mark', 1);
            session_destroy();
            header('Location: ./');
        }
        exit();
    }

    $tempPhone = preg_replace('/\D/', '', $phone);

    function emptyValue($cook, $comment, $usl) {
        global $err;
        $res = false;
        $setVal = $_POST[$cook];
        if ($usl) {
            setcookie($cook . '_err', $comment, time() + 24 * 60 * 60);
            $err = true;
            $res = true;
        }
        if ($cook == 'lang') {
            global $lang;
            $setVal = ($lang != '') ? implode(",", $lang) : '';
        }
        setcookie($cook . '_val', $setVal, time() + 30 * 24 * 60 * 60);
        return $res;
    }

    if (!emptyValue('name', 'поле необходимо заполнить', empty($name))) {
        if (!emptyValue('name', 'некорректная длина поля', strlen($name) > 255)) {
            emptyValue(
                'name',
                'поле не соответствует требованиям',
                !preg_match('/^([а-яёА-ЯЁ]+-?[а-яёА-ЯЁ]+)( [а-яёА-ЯЁ]+-?[а-яёА-ЯЁ]+){1,2}$/Diu', $name)
            );
        }
    }

    if (!emptyValue('phone', 'поле необходимо заполнить', empty($phone))) {
        if (!emptyValue('phone', 'некорректная длинна поля', strlen($phone) != 11)) {
            emptyValue('phone', 'поле не должно содержать нецифровых символов', ($phone != $tempPhone));
        }
    }

    if (!emptyValue('email', 'поле необходимо заполнить', empty($email))) {
        if (!emptyValue('email', 'некорректная длина поля', strlen($email) > 255)) {
            emptyValue(
                'email', 'поле не соответствует паттерну example@mail.ru',
                !preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email)
            );
        }
    }

    if (!emptyValue('birthday', "выберите дату рождения", empty($birthday))) {
        emptyValue(
            'birthday',
            "неверная дата рождения",
            (strtotime("now") < strtotime($birthday))
        );
    }

    emptyValue('gender', "выберите пол", (empty($gender) || !preg_match('/^(m|f)$/', $gender)));
    if (!emptyValue('lang', "необходимо выбрать минимум 1 язык", empty($lang))) {
        try {
            $inQuery = implode(',', array_fill(0, count($lang), '?'));
            $dbLangs = $db->prepare("SELECT id, name FROM languages WHERE name IN ($inQuery)");
            foreach ($lang as $key => $value) {
                $dbLangs->bindValue(($key + 1), $value);
            }
            $dbLangs->execute();
            $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print ('Error : ' . $e->getMessage());
            exit();
        }

        emptyValue('lang', 'неверно выбраны языки', $dbLangs->rowCount() != count($lang));
    }
    if (!emptyValue('bio', 'поле необходимо заполнить', empty($bio))) {
        emptyValue('bio', 'слишком длинный текст', strlen($bio) > 65535);
    }
    emptyValue('check_mark', "ознакомьтесь с соглашением", empty($check_mark));

    if ($err) {
        header('Location: index.php');
        exit();
    } else {
        deleteCookies('name');
        deleteCookies('phone');
        deleteCookies('email');
        deleteCookies('birthday');
        deleteCookies('gender');
        deleteCookies('lang');
        deleteCookies('bio');
        deleteCookies('check_mark');
    }

    if ($log) {
        $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, email = ?, birthday = ?, gender = ?, bio = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, strtotime($birthday), $gender, $bio, $_SESSION['id']]);
        $stmt = $db->prepare("DELETE FROM users_languages WHERE user_id = ?");
        $stmt->execute([$_SESSION['id']]);
        $stmt1 = $db->prepare("INSERT INTO users_languages (user_id, lang_id) VALUES (?, ?)");
        foreach($languages as $row){
            $stmt1->execute([$_SESSION['id'], $row['id']]);
            if ($adminLog) {
                setcookie('admin_value', '1', time() + 30 * 24 * 60 * 60);
            }
        }
    } else {
        $login = substr(uniqid(), 0, 4).rand(10, 100);
        $password = rand(100, 1000).substr(uniqid(), 4, 10);
        setcookie('login', $login);
        setcookie('password', $password);
        $mpassword = md5($password);
        try {
            $stmt = $db->prepare("INSERT INTO verified_users (login, password) VALUES (?, ?)");
            $stmt->execute([$login, $mpassword]);
            $user_id = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO users (name, phone, email, birthday, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, strtotime($birthday), $gender, $bio]);
            $fid = $db->lastInsertId();
            $stmt1 = $db->prepare('INSERT INTO users_languages (user_id, lang_id) VALUES (?, ?)');
            foreach ($languages as $row) {
                $stmt1->execute([$fid, $row['id']]);
            }
        } catch (PDOException $e) {
            print ('Error : ' . $e->getMessage());
            exit();
        }
        setcookie('name_val', $name, time() + 24 * 60 * 60 * 365);
        setcookie('phone_val', $phone, time() + 24 * 60 * 60 * 365);
        setcookie('email_val', $email, time() + 24 * 60 * 60 * 365);
        setcookie('birthday_val', $birthday, time() + 24 * 60 * 60 * 365);
        setcookie('gender_val', $gender, time() + 24 * 60 * 60 * 365);
        setcookie('lang_val', implode(',', $lang), time() + 24 * 60 * 60 * 365);
        setcookie('bio_val', $bio, time() + 24 * 60 * 60 * 365);
        setcookie('check_mark_val', $check_mark, time() + 24 * 60 * 60 * 365);
    }
    setcookie('save', '1');
    header('Location: index.php' . (($getUid != NULL) ? '?uid=' . $uid : ''));
}

function deleteCookies($cook, $vals = 0) {
    setcookie($cook.'_error', '', 100000);
    if($vals) {
        setcookie($cook.'_value', '', 100000);
    }
}
?>