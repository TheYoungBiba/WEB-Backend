<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="styles.css"/>
    <title>Task3</title>
</head>

<form action="" method="post" class="form-container">
    <div class="form-wrapper">
        <div class="form-header">
            <h2><b>Form</b></h2>
        </div>
        <?php
        if($log) echo '<button type="submit" class="logout_form" name="logout_form">Выйти</button>';
        else echo '<a href="login.php" class="login_form" name="logout_form">Войти</a>';
        ?>
        <div class="message"><?php if(isset($mess['success'])) echo $mess['success']; ?></div>
        <div class="message message_info"><?php if(isset($mess['info'])) echo $mess['info']; ?></div>
        <div class="form-input">
            <label>
                <input
                        type="text"
                        name="name"
                        class="input <?php echo ($errs['name'] != NULL) ? 'borred' : ''; ?>"
                        value="<?php echo $vals['name']; ?>" placeholder="ФИО"
                />
            </label>
            <div class="error"> <?php echo $mess['name'] ?> </div>
        </div>

        <div class="form-input">
            <label>
                <input
                        type="tel"
                        name="phone"
                        class="input <?php echo ($errs['phone'] != NULL) ? 'borred' : ''; ?>"
                        value="<?php echo $vals['phone']; ?>" placeholder="Введите номер телефона"
                />
            </label>
            <div class="error"> <?php echo $mess['phone'] ?> </div>
        </div>

        <div class="form-input">
            <label>
                <input
                        name="email"
                        type="email"
                        class="input <?php echo ($errs['email'] != NULL) ? 'borred' : ''; ?>"
                        value="<?php echo $vals['email']; ?>" placeholder="Введите адрес электронной почты"
                />
            </label>
            <div class="error"> <?php echo $mess['email'] ?> </div>
        </div>

        <div class="form-input">
            <label>
                <input
                        name="birthday"
                        class="input <?php echo ($errs['birthday'] != NULL) ? 'borred' : ''; ?>"
                        value="<?php if ($vals['birthday'] > 100000) echo $vals['birthday']; ?>" type="date"
                />
            </label>
            <div class="error"> <?php echo $mess['birthday'] ?> </div>
        </div>

        <div class="form-radio">Пол<br/>
            <label>
                <input type="radio" name="gender" value="m"/>
                <span class="<?php echo ($errs['gender'] != NULL) ? 'colred' : ''; ?>">Муж</span>
            </label>
            <label>
                <input type="radio" name="gender" value="f"/>
                <span class="<?php echo ($errs['gender'] != NULL) ? 'colred' : ''; ?>">Жен</span>
            </label>
            <div class="error"> <?php echo $mess['gender'] ?> </div>
        </div>

        <div class="form-input">
            <label class="input">
                Выберите любимый язык<br/>
                <select id="lang" name="lang[]" multiple="multiple"
                        class="<?php echo ($errs['lang'] != NULL) ? 'borred' : ''; ?>">
                    <option value="Pascal" <?php echo (in_array('Pascal', $langs)) ? 'selected' : ''; ?>>Pascal</option>
                    <option value="C" <?php echo (in_array('C', $langs)) ? 'selected' : ''; ?>>C</option>
                    <option value="C++" <?php echo (in_array('C++', $langs)) ? 'selected' : ''; ?>>C++</option>
                    <option value="JavaScript" <?php echo (in_array('JavaScript', $langs)) ? 'selected' : ''; ?>>
                        JavaScript
                    </option>
                    <option value="PHP" <?php echo (in_array('PHP', $langs)) ? 'selected' : ''; ?>>PHP</option>
                    <option value="Python" <?php echo (in_array('Python', $langs)) ? 'selected' : ''; ?>>Python</option>
                    <option value="Java" <?php echo (in_array('Java', $langs)) ? 'selected' : ''; ?>>Java</option>
                    <option value="Haskel" <?php echo (in_array('Haskel', $langs)) ? 'selected' : ''; ?>>Haskel</option>
                    <option value="Clojure" <?php echo (in_array('Clojure', $langs)) ? 'selected' : ''; ?>>Clojure
                    </option>
                    <option value="Scala" <?php echo (in_array('Scala', $langs)) ? 'selected' : ''; ?>>Scala</option>
                </select>
            </label>
            <div class="error"> <?php echo $mess['lang'] ?> </div>
        </div>

        <div class="form-input">Биография <br/>
            <label>
                <textarea name="bio" class="input <?php echo ($errs['bio'] != NULL) ? 'borred' : ''; ?>" placeholder="Биография"><?php echo $vals['bio']; ?><?php echo checkSQL($vals['bio']); ?></textarea>
            </label>
            <div class="error"> <?php echo $mess['bio'] ?> </div>
        </div>

        <div class="form-checkbox">
            <label for="check_mark">
                <input
                        type="checkbox"
                        name="check_mark"
                        id="check_mark" <?php echo ($vals['check_mark'] != NULL) ? 'checked' : ''; ?>>
                С политикой конфиденциальности ознакомлен(а)
            </label>
            <div class="error"> <?php echo $mess['check_mark'] ?> </div>
        </div>

        <button type="submit" class="form-button">Отправить</button>
    </div>
<!--    --><?php
//        if($log) {
//            echo '<button class="button edbut" type="submit">Изменить</button>';
//        } else {
//            echo '<button class="button" type="submit">Отправить</button>';
//        }
//        if($log) {
//            echo '<button class="button" type="submit" name="logout_form">Выйти</button>';
//        } else {
//            echo '<a class="btnlike" href="login.php" name="logout_form">Войти</a>';
//        }
//    ?>
</form>