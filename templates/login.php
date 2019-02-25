<?=$categories_list;?>
<?php
$email = $_POST['email'] ?? '';
?>
<?php if($errors): ?>
<form class="form container form--invalid" action="login.php" method="post"> <!-- form--invalid -->
<?php else: ?>
<form class="form container" action="login.php" method="post">
<?php endif; ?>
    <h2>Вход</h2>
    <?php if(isset($errors['email'])): ?>
    <div class="form__item form__item--invalid"> <!-- form__item--invalid -->
    <?php else: ?>
    <div class="form__item">
    <?php endif; ?>   
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$email;?>" required>
        <?php if(isset($errors['email'])): ?>
        <span class="form__error"><?=$errors['email'];?></span>
        <?php endif; ?>
    </div>
    <?php if(isset($errors['password'])): ?>
    <div class="form__item form__item--last form__item--invalid">
    <?php else: ?>
    <div class="form__item form__item--last">
    <?php endif; ?>
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <?php if(isset($errors['password'])): ?>
        <span class="form__error"><?=$errors['password'];?></span>
        <?php endif; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
