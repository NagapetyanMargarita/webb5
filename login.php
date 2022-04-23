<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход в систему</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  session_destroy();
  // Делаем перенаправление на форму.
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if (!empty($_GET['nologin']))
    print("<div>Пользователя с таким логином не существует</div>");
  if (!empty($_GET['wrongpass']))
    print("<div>Неверный пароль!</div>");

?>

  <form action="" method="post">
    <input name="login" placeholder="Введи логин"/>
    <input name="pass" placeholder="Введи пароль"/>
    <input type="submit" id="login" value="Войти" />
  </form>

  <?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  $db = new PDO('mysql:host=localhost;dbname=u46981', 'u46981', '3843607', array(PDO::ATTR_PERSISTENT => true));
  $stmt = $db->prepare("SELECT human_id, pass FROM login_pass WHERE login = ?");
  $stmt -> execute([$_POST['login']]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    header('Location: ?nologin=1');
    exit();
  }
  if($row["pass"] != md5($_POST['pass'])) {
    header('Location: ?wrongpass=1');
    exit();
  }
  // Если все ок, то авторизуем пользователя.
  $_SESSION['login'] = $_POST['login'];
  // Записываем ID пользователя.
  $_SESSION['uid'] = $row["human_id"];

  // Делаем перенаправление.
  header('Location: ./');
}

?>

</body>
</html>