<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();
  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены. ';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),//очистки строки от тегов
        strip_tags($_COOKIE['pass']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['name'] = !empty($_COOKIE['name_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['year'] = !empty($_COOKIE['year_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['limbs'] = !empty($_COOKIE['limbs_error']);
  $errors['checkbox'] = !empty($_COOKIE['checkbox_error']);

  // Выдаем сообщения об ошибках.
  if ($errors['name']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('name_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div>Заполните имя.</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div>Некорректный email.</div>';
  }
  if ($errors['year']) {
    setcookie('year_error', '', 100000);
    $messages[] = '<div>Выберите год рождения.</div>';
  }
  if ($errors['gender']) {
    setcookie('gender_error', '', 100000);
    $messages[] = '<div>Выберите пол.</div>';
  }
  if ($errors['limbs']) {
    setcookie('limbs_error', '', 100000);
    $messages[] = '<div>Выберите количество конечностей.</div>';
  }
  if ($errors['checkbox']) {
    setcookie('checkbox_error', '', 100000);
    $messages[] = '<div>Поставьте галочку.</div>';
  }

  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['year'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : $_COOKIE['limbs_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
  $values['checkbox'] = empty($_COOKIE['checkbox_value']) ? '' : $_COOKIE['checkbox_value']; 
  if(empty($_COOKIE['ability_value']))
    $values['ability'] = array();
  else 
    $values['ability'] = json_decode($_COOKIE['ability_value'], true);
  
  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  session_start();
  if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
    // загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    $db = new PDO('mysql:host=localhost;dbname=u46981', 'u46981', '3843607', array(PDO::ATTR_PERSISTENT => true));
    
    $stmt = $db->prepare("SELECT * FROM human WHERE id = ?");
    $stmt -> execute([$_SESSION['uid']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $values['name'] = strip_tags($row['name']);
    $values['email'] = strip_tags($row['email']);
    $values['year'] = $row['year'];
    $values['gender'] = $row['gender'];
    $values['limbs'] = $row['limbs'];
    $values['bio'] = strip_tags($row['bio']);
    $values['checkbox'] = true; 

    $stmt = $db->prepare("SELECT * FROM superability WHERE human_id = ?");
    $stmt -> execute([$_SESSION['uid']]);
    $ability = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      array_push($ability, strip_tags($row['name_of_superability']));
    }
    $values['ability'] = $ability;
    
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty(htmlentities($_POST['name']))) {
    // Выдаем куку на день с флажком об ошибке в поле name.
    setcookie('name_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на год.
    setcookie('name_value', $_POST['name'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('email_value', $_POST['email'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['year'])) {
    setcookie('year_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('year_value', $_POST['year'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['gender'])) {
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('gender_value', $_POST['gender'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['limbs'])) {
    setcookie('limbs_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('limbs_value', $_POST['limbs'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if (empty($_POST['checkbox'])) {
    setcookie('checkbox_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('checkbox_value', $_POST['checkbox'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if(!empty($_POST['bio'])){
    setcookie ('bio_value', $_POST['bio'], time() + 12 * 30 * 24 * 60 * 60);
  }
  if(!empty($_POST['ability'])){
    $json = json_encode($_POST['ability']);
    setcookie ('ability_value', $json, time() + 12 * 30 * 24 * 60 * 60);
  }

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('year_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('limbs_error', '', 100000);
    setcookie('checkbox_error', '', 100000);
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // Перезаписываем данные в БД новыми данными,
    // кроме логина и пароля.
    $db = new PDO('mysql:host=localhost;dbname=u46981', 'u46981', '3843607', array(PDO::ATTR_PERSISTENT => true));
    
    // Обновление данных в таблице human
    $stmt = $db->prepare("UPDATE human SET name = ?, email = ?, year = ?, gender = ?, limbs = ?, bio = ? WHERE id= ?");
    $stmt -> execute([$_POST['name'], $_POST['email'], $_POST['year'], $_POST['gender'], $_POST['limbs'], $_POST['bio'],$_SESSION['uid']]);

    // Обновление данных в таблице superability
    $stmt = $db->prepare("DELETE FROM superability WHERE human_id = ?");
    $stmt -> execute([$_SESSION['uid']]);

    $ability = $_POST['ability'];

    foreach($ability as $item) {
      $stmt = $db->prepare("INSERT INTO superability SET human_id = ?, name_of_superability = ?");
      $stmt -> execute([$_SESSION['uid'], $item]);
    }
  }
  else {
    // Генерируем уникальный логин и пароль.
    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    $max=rand(8,16);
    $size=StrLen($chars)-1;
    $pass=null;
    while($max--)
      $pass.=$chars[rand(0,$size)];
    $login = $chars[rand(0,25)] . strval(time());
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

    // Сохранение данных формы, логина и хеш md5() пароля в базу данных.
    $db = new PDO('mysql:host=localhost;dbname=u46981', 'u46981', '3843607', array(PDO::ATTR_PERSISTENT => true));

    // Запись в таблицу human
    $stmt = $db->prepare("INSERT INTO human SET name = ?, email = ?, year = ?, gender = ?, limbs = ?, bio = ?");
    $stmt -> execute([$_POST['name'], $_POST['email'], $_POST['year'], $_POST['gender'], $_POST['limbs'], $_POST['bio']]);
    
    // Узнаём id последней записи в таблице human
    $res = $db->query("SELECT max(id) FROM human");
    $row = $res->fetch();
    $count = (int) $row[0];

    $ability = $_POST['ability'];

    foreach($ability as $item) {
      // Запись в таблицу superability
      $stmt = $db->prepare("INSERT INTO superability SET human_id = ?, name_of_superability = ?");
      $stmt -> execute([$count, $item]);
    }

    // Запись в таблицу login_pass
    $stmt = $db->prepare("INSERT INTO login_pass SET human_id = ?, login = ?, pass = ?");
    $stmt -> execute([$count, $login, md5($pass)]);
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
