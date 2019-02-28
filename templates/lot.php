<?=$categories_list;?>
    <section class="lot-item container">
      <h2><?=strip_tags($lot['name']);?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=$lot['image'];?>" width="730" height="548" alt="<?=strip_tags($lot['name']);?>">
          </div>
          <p class="lot-item__category">Категория: <span><?=$lot['category'];?></span></p>
          <p class="lot-item__description"><?=strip_tags($lot['description']);?></p>
        </div>
        <div class="lot-item__right">
          <?php if(!$is_end): ?>
          <div class="lot-item__state">
            <div class="lot-item__timer timer">
              <?=get_time_of_end_lot($lot['time']);?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=show_price($total_rate);?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=show_price($lot['step']);?></span>
              </div>
            </div>
            <?php if($is_auth === 1 && !$is_end): ?>
            <form class="lot-item__form" action="lot.php" method="post">
              <?php if($error): ?>
              <p class="lot-item__form-item form__item form__item--invalid">
              <?php else: ?>
              <p class="lot-item__form-item form__item">
              <?php endif; ?>
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="12 000">
                <input type="hidden" name="id" value="<?=$lot_id;?>">
                <?php if($error): ?>
                <span class="form__error"><?=$error;?></span>
                <?php endif; ?>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <?=$rates;?>
        </div>
      </div>
    </section>