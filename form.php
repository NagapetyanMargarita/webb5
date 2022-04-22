<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
   <!-- Bootstrap Bundle JS (jsDelivr CDN) -->
   <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
   <title>Задание 4</title>
   <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

  <?php
  if (!empty($messages)) {
    print('<div class="messages">');
    // Выводим все сообщения.
    foreach ($messages as $message) {
      print($message);
    }
    print('</div>');
  }
  // Далее выводим форму отмечая элементы с ошибками классом error
  // и задавая начальные значения элементов ранее сохраненными.
  ?>

<div class="d-flex flex-column flex-nowrap">
   <div class="order-md-5 order-sm-4 order-4">
    <div class="fon tab mt-4 mb-4 shadow rounded" id="quf">
    <h1 id="form1">Форма:</h1>
    <form id="form" action="" method="POST">
    <div class="form-group">
    <legend for="username" style="color: black;">Имя:</legend>
      <input type="text" name="name"  <?php if ($errors['name']) {print 'class="pole error"';} else print 'class="pole"'; ?> value="<?php print $values['name']; ?>">
</div>
      <div class="form-group">
      <legend for="email" style="color: black;">E-mail:</legend>
      <input type="text" name="email" <?php if ($errors['email']) {print 'class="pole error';} else print 'class="pole"'; ?> value="<?php print $values['email']; ?>">
</div>
<br>
<div class="form-group">
    <legend for="date"class="pole" style="color: black;">Дата рождения:</legend>
      <input type="date" id="date" size="3" name="date" <?php if ($errors['date']) {print 'class="pole error"';} else print 'class="pole"';?> value="<?php print $values['date']; ?>">
      </div>
      <br>
      <fieldset class="form-group">
      <div <?php if ($errors['gender']) {print 'class="error"';} ?>>
      <legend style="color: black;">Выберите пол:</legend>
        <input class="radio" type="radio" name="gender" value="men" <?php if ($values['gender'] == 'men') {print 'checked';} ?>> Мужской
        <input class="radio" type="radio" name="gender" value="women" <?php if ($values['gender'] == 'women') {print 'checked';} ?>> Женский
      </div>
      </fieldset>
      <br>
      <fieldset class="form-group">
      <div <?php if ($errors['limbs']) {print 'class="error"';} ?>>
      <legend style="color: black;">Выберите кол-во конечностей:</legend>
        <input class="radio" type="radio" name="limbs" value="1" <?php if ($values['limbs'] == '1') {print 'checked';} ?>> 1
        <input class="radio" type="radio" name="limbs" value="2" <?php if ($values['limbs'] == '2') {print 'checked';} ?>> 2
        <input class="radio" type="radio" name="limbs" value="3" <?php if ($values['limbs'] == '3') {print 'checked';} ?>> 3
        <input class="radio" type="radio" name="limbs" value="4" <?php if ($values['limbs'] == '4') {print 'checked';} ?>> 4
        <input class="radio" type="radio" name="limbs" value="5" <?php if ($values['limbs'] == '5') {print 'checked';} ?>> 5 
      </div>
      </fieldset>
      <br>
      <div class="form-group">
      <legend for="multipleselect" style="color: black;">Выберите сверхспособности:</legend>
      <select class="group" name="ability[]" size="3" class="pole" multiple>
          <option value="Бессмертие" <?php if (in_array("Бессмертие", $values['ability'])) {print 'selected';} ?>>Бессмертие</option>
          <option value="Прохождение сквозь стены" <?php if (in_array("Прохождение сквозь стены", $values['ability'])) {print 'selected';} ?>>Прохождение сквозь стены</option>
          <option value="Левитация" <?php if (in_array("Левитация", $values['ability'])) {print 'selected';} ?>>Левитация</option>
      </select>
          </div>
          <br>
      <div class="form-group" >
      <legend for="textarea" style="color: black;">Биография: </legend>
      <textarea class="group" name="bio" rows="5" id="pole"><?php print $values['bio']; ?></textarea>
          </div>
          <br>
          <fieldset class="form-group">
      <div  <?php if ($errors['checkbox']) {print 'class="error"';} ?>>
        <input type="checkbox" name="checkbox" <?php if ($values['checkbox']) {print 'checked';} ?>> С контрактом ознакомлен(a) 
      </div>
      </fieldset>
      <input type="submit" id="send" class="btn btn-success btn-md" value="Отправить">
    </form>
  </div>
  <div class="container">
    <?php
      if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login']))
        print('<a href="login.php" class = "enter-exit" title = "Log out">Выйти</a>');
      else
        print('<a href="login.php" class = "enter-exit"  title = "Log in">Войти</a>');
    ?>
  </div>
</body>
</html>