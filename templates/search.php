<?=$categories_list;?>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=$search_str;?></span>»</h2>
        <ul class="lots__list">
            <?php foreach($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$lot['image'];?>" width="350" height="260" alt="<?=htmlspecialchars($lot['category']);?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($lot['category']);?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['id'];?>"><?=strip_tags($lot['name']);?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=show_price($lot['cost']);?></span>
                        </div>
                        <div class="lot__timer timer">
                            <?=get_time_of_end_lot($lot['time']);?> <!-- Формат dd:mm:ss. Потребуется чуть доработать функцию для получения нужного формата-->
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
      </section>
      <?=$paginator;?>
</div>
