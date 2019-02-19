<?=$categories_list;?>
<form class="form form--add-lot container <?php if($errors): ?>form--invalid<?php endif;?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?php if(isset($errors['lot-name'])):?>form__item--invalid<?php endif; ?>"> <!-- form__item--invalid -->
          <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" required>
          <?php if(isset($errors['lot-name'])):?>
          <span class="form__error"><?=$errors['lot-name'];?></span>
          <?php endif; ?>
        </div>
        <div class="form__item <?php if(isset($errors['category'])):?>form__item--invalid<?php endif; ?>">
          <label for="category">Категория</label>
          <select id="category" name="category" required>
            <option>Выберите категорию</option>
            <?php foreach ($categories as $value): ?>
              <option><?=$value;?></option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errors['category'])):?>
          <span class="form__error"><?=$errors['category'];?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="form__item form__item--wide <?php if(isset($errors['message'])):?>form__item--invalid<?php endif; ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота" required></textarea>
        <?php if(isset($errors['message'])):?>
        <span class="form__error"><?=$errors['message'];?></span>
        <?php endif; ?>
      </div>

      <div class="form__item form__item--file"> <!-- form__item--uploaded -->

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
        <?php if(isset($errors['lot-date'])):?>
        <span class="form__error"><?=$errors['image'];?></span>
        <?php endif; ?>

      </div>

      <div class="form__container-three">
        <div class="form__item form__item--small <?php if(isset($errors['lot-rate'])):?>form__item--invalid<?php endif; ?>">
          <label for="lot-rate">Начальная цена</label>
          <input id="lot-rate" type="number" name="lot-rate" placeholder="0" required>
          <?php if(isset($errors['lot-rate'])):?>
          <span class="form__error"><?=$errors['lot-rate'];?></span>
          <?php endif; ?>
        </div>
        <div class="form__item form__item--small <?php if(isset($errors['lot-step'])):?>form__item--invalid<?php endif; ?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="number" name="lot-step" placeholder="0" required>
          <?php if(isset($errors['lot-step'])):?>
          <span class="form__error"><?=$errors['lot-step'];?></span>
          <?php endif; ?>
        </div>
        <div class="form__item <?php if(isset($errors['lot-date'])):?>form__item--invalid<?php endif; ?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="date" name="lot-date" required>
          <?php if(isset($errors['lot-date'])):?>
          <span class="form__error"><?=$errors['lot-date'];?></span>
          <?php endif; ?>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button">Добавить лот</button>
    </form>
