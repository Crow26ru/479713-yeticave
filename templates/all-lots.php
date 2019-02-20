<?=$categories_list;?>

    <div class="container">
      <section class="lots">
        <h2>Все лоты в категории <span><?=$category;?></span></h2>
        <ul class="lots__list">
          <?php foreach($lots as $lot): ?>
          <li class="lots__item lot">
            <div class="lot__image">
              <img src="<?=$lot['image'];?>" width="350" height="260" alt="<?=$lot['category'];?>">
            </div>
            <div class="lot__info">
              <span class="lot__category"><?=$lot['image'];?></span>
              <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['id'];?>"><?=strip_tags($lot['name']);?></a></h3>
              <div class="lot__state">
                <div class="lot__rate">
                  <span class="lot__amount">Стартовая цена</span>
                  <span class="lot__cost"><?=show_price($lot['start_cost']);?></span>
                </div>
                <div class="lot__timer timer">
                  <?=get_time_of_end_lot($lot['time'], true);?> <!-- Формат hh:mm:ss -->
                </div>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?=$paginator;?>
    </div>
