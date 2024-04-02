<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css" />
    <title>Task3</title>
</head>

<form action="" method="post" class="form-container">
    <div class="form-wrapper">
        <div class="form-header">
            <h2><b>Form</b></h2>
        </div>


        <div class="form-input">
            <label>
                <input
                        type="text"
                        name="name"
                        class="input"
                        placeholder="ФИО"
                />
            </label>
        </div>


        <div class="form-input">
            <label>
                <input
                        type="tel"
                        name="number"
                        class="input"
                        placeholder="Введите номер телефона"
                />
            </label>
        </div>


        <div class="form-input">
            <label>
                <input
                        name="email"
                        type="email"
                        class="input"
                        placeholder="Введите адрес электронной почты"
                />
            </label>
        </div>


        <div class="form-input">
            <label>
                <input name="data"
                       class="input"
                       type="date"
                />
            </label>
        </div>


        <div class="form-radio">
            Пол
            <br />
            <label>
                <input type="radio" name="radio" value="m" />
                Муж
            </label>
            <label>
                <input type="radio" name="radio" value="f" />
                Жен
            </label>
        </div>


        <div class="form-input">
            <label class="input">
                Выберите любимый язык<br />
                <select id="lang" name="lang[]" multiple="multiple">
                    <option value="Pascal">Pascal</option>
                    <option value="C">C</option>
                    <option value="C++">C++</option>
                    <option value="JavaScript">JavaScript</option>
                    <option value="PHP">PHP</option>
                    <option value="Python">Python</option>
                    <option value="Java">Java</option>
                    <option value="Haskel">Haskel</option>
                    <option value="Clojure">Clojure</option>
                    <option value="Scala">Scala</option>
                </select>
            </label>
        </div>

        <div class="form-input">
            Биография <br />
            <label>
          <textarea name="biography" class="input" placeholder="Биография">
          </textarea>
            </label>
        </div>

        <div class="form-checkbox">
            <label for="oznakomlen">
                <input type="checkbox" name="check_mark" id="oznakomlen" />
                С политикой конфиденциальности ознакомлен(а)
            </label>
        </div>

        <button type="submit" class="form-button">Отправить</button>
    </div>
</form>