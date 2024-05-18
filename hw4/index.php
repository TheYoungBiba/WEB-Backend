<?php
header('Content-Type: text/html; charset=UTF-8');

$db = new PDO(
    [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
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

    function emptyValue($enName, $val)
    {
        global $errs, $vals, $mess;
        $errs[$enName] = !empty($_COOKIE[$enName . '_err']);
        $mess[$enName] = "<div class='messageError'>$val</div>";
        $vals[$enName] = empty($_COOKIE[$enName . '_val']) ? '' : $_COOKIE[$enName . '_val'];
        deleteCookies($enName);
    }

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $mess['success'] = '<div class="message">сохранено</div>';
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
    $err = false;
    $tempPhone = preg_replace('/\D/', '', $phone);

    function emptyValue($cook, $comment, $usl)
    {
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

    try {
        $stmt = $db->prepare("INSERT INTO users (name, phone, email, birthday, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, strtotime($birthday), $gender, $bio]);
        $fid = $db->lastInsertId();
        $stmt1 = $db->prepare("INSERT INTO users_languages (user_id, lang_id) VALUES (?, ?)");
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

    setcookie('save', '1');

    header('Location: index.php');
}

function deleteCookies($cook)
{
    setcookie($cook . '_err', '', time() - 30 * 24 * 60 * 60);
}
?>