<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрационная форма</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 50%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 5px;
            display: block;
        }

        inputtype="text",
        inputtype="tel",
        inputtype="email",
        inputtype="date",
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        inputtype="checkbox",
        inputtype="radio" {
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>

</head>

<body>

<div class="container">
    <h2>Регистрационная форма</h2>
    <form>
        <label for="fullname">ФИО:</label>
        <input type="text" id="fullname" name="fullname">

        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone">

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email">

        <label for="dob">Дата рождения:</label>
        <input type="date" id="dob" name="dob">

        <label>Пол:</label>
        <input type="radio" id="male" name="gender" value="male">
        <label for="male">Мужской</label>
        <input type="radio" id="female" name="gender" value="female">
        <label for="female">Женский</label>

        <label for="programming-language">Любимый язык программирования:</label>
        <select id="programming-language" name="programming-language" multiple>
            <option value="pascal">Pascal</option>
            <option value="c">C</option>
            <option value="c++">C++</option>
            <option value="javascript">JavaScript</option>
            <option value="php">PHP</option>
            <option value="python">Python</option>
            <option value="java">Java</option>
            <option value="haskel">Haskel</option>
            <option value="clojure">Clojure</option>
            <option value="prolog">Prolog</option>
            <option value="scala">Scala</option>
        </select>

        <label for="bio">Биография:</label>
        <textarea id="bio" name="bio" rows="4"></textarea>

        <input type="checkbox" id="contract" name="contract">
        <label for="contract">Я с контрактом ознакомлен(а)</label>

        <button type="submit">Сохранить</button>
    </form>
</div>

</body>

</html>