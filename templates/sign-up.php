<?=$categories_list;?>

<?php
$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';
$message = $_POST['message'] ?? '';
?>

<?php if($errors): ?>
<form class="form container form--invalid" action="sign-up.php" method="post" enctype="multipart/form-data">

<?php else: ?>
<form class="form container" action="sign-up.php" method="post" enctype="multipart/form-data">
<?php endif; ?>

    <h2>Регистрация нового аккаунта</h2>

    <?php if(isset($errors['email'])): ?>
    <div class="form__item form__item--invalid"> <!-- form__item--invalid -->

    <?php else: ?>
    <div class="form__item">
    <?php endif; ?>

        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" value="<?=$email;?>" placeholder="Введите e-mail" required>

        <?php if(isset($errors['email'])): ?>
        <span class="form__error"><?=$errors['email'];?></span>
        <?php endif; ?>

    </div>

    <?php if(isset($errors['password'])): ?>
    <div class="form__item form__item--invalid">

    <?php else: ?>
    <div class="form__item">
    <?php endif; ?>

        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>

        <?php if(isset($errors['password'])): ?>
        <span class="form__error"><?=$errors['password'];?></span>
        <?php endif; ?>

    </div>

    <?php if(isset($errors['name'])): ?>
    <div class="form__item form__item--invalid">

    <?php else: ?>
    <div class="form__item">
    <?php endif; ?>

        <label for="name">Имя*</label>
        <input id="name" type="text" name="name" value="<?=$name;?>" placeholder="Введите имя" required>

        <?php if(isset($errors['name'])): ?>
        <span class="form__error"><?=$errors['name'];?></span>
        <?php endif; ?>

    </div>

    <?php if(isset($errors['message'])): ?>
    <div class="form__item form__item--invalid">

    <?php else: ?>
    <div class="form__item">
    <?php endif; ?>

        <label for="message">Контактные данные*</label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться" required><?=$message;?></textarea>

        <?php if(isset($errors['message'])): ?>
        <span class="form__error"><?=$errors['message'];?></span>
        <?php endif; ?>

    </div>

    <?php if(isset($errors['image'])): ?>
    <div class="form__item form__item--file form__item--last form__item--invalid">

    <?php else: ?>
    <div class="form__item form__item--file form__item--last">
    <?php endif; ?>

        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="image" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>

           <?php if(isset($errors['image'])): ?>
           <span class="form__error"><?=$errors['image'];?></span>
           <?php endif; ?>

        </div>
    </div>

    <?php if($errors): ?>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif; ?>

    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
</form>
