<?=$categories_list;?>
<?php
$name = $_POST['lot-name'] ?? '';
$category = $_POST['category'] ?? '';
$message = $_POST['message'] ?? '';
$image = $_FILES['image']['name'] ?? '';
$start_cost = $_POST['lot-rate'] ?? '';
$step = $_POST['lot-step'] ?? '';
$date = $_POST['lot-date'] ?? '';
?>
<form class="form form--add-lot container <?=$errors ? 'form--invalid' : '';?>" action="add.php" method="post" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?=isset($errors['lot-name'])? 'form__item--invalid' : '';?>">
          <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" name="lot-name" value="<?=$name;?>" placeholder="Введите наименование лота" required>
          <?php if(isset($errors['lot-name'])):?>
          <span class="form__error"><?=$errors['lot-name'];?></span>
          <?php endif; ?>
        </div>
        <div class="form__item <?=isset($errors['category']) ? 'form__item--invalid' : '';?>">
          <label for="category">Категория</label>
          <select id="category" name="category" value="<?=htmlspecialchars($category);?>" required>
            <option>Выберите категорию</option>
            <?php foreach ($categories as $value): ?>
              <option <?=$category === $value ? 'selected' : '';?>><?=htmlspecialchars($value);?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['category'])):?>
          <span class="form__error"><?=$errors['category'];?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="form__item form__item--wide <?=isset($errors['message']) ? 'form__item--invalid' : '';?>">
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота" required><?=$message;?></textarea>
        <?php if(isset($errors['message'])):?>
        <span class="form__error"><?=$errors['message'];?></span>
        <?php endif; ?>
      </div>
      <div class="form__item form__item--file <?=$image ? 'form__item--uploaded' : '';?> <?=isset($errors['image']) ? 'form__item--invalid' : '';?>">
        <label>Изображение</label>
        <div class="preview">
          <button class="preview__remove" type="button">x</button>
          <div class="preview__img">
            <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
          </div>
        </div>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="photo2" name="image" value="">
          <label for="photo2">
            <span>+ Добавить</span>
          </label>
        </div>
        <?php if(isset($errors['image'])):?>
        <span class="form__error"><?=$errors['image'];?></span>
        <?php endif; ?>
      </div>
      <div class="form__container-three">
        <div class="form__item form__item--small <?=isset($errors['lot-rate']) ? 'form__item--invalid' : '';?>">
          <label for="lot-rate">Начальная цена</label>
          <input id="lot-rate" type="text" name="lot-rate" value="<?=$start_cost;?>" placeholder="0" required>
          <?php if(isset($errors['lot-rate'])):?>
          <span class="form__error"><?=$errors['lot-rate'];?></span>
          <?php endif; ?>
        </div>
        <div class="form__item form__item--small <?=isset($errors['lot-step']) ? 'form__item--invalid' : '';?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="text" name="lot-step" value="<?=$step;?>" placeholder="0" required>
          <?php if(isset($errors['lot-step'])):?>
          <span class="form__error"><?=$errors['lot-step'];?></span>
          <?php endif; ?>
        </div>
        <div class="form__item <?=isset($errors['lot-date']) ? 'form__item--invalid' : '';?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="text" name="lot-date" value="<?=$date;?>" required>
          <?php if(isset($errors['lot-date'])):?>
          <span class="form__error"><?=$errors['lot-date'];?></span>
          <?php endif; ?>
        </div>
      </div>
      <?php if($errors): ?>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <?php endif;?>
      <button type="submit" class="button">Добавить лот</button>
    </form>
