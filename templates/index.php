<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php
            foreach($categories as $value):
        ?>
        <li class="promo__item promo__item--boards">
            <a class="promo__link" href="./all-lots.php?id=<?=$value['id'];?>"><?=htmlspecialchars($value['categories']);?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php
            foreach($products as $position => $item):
        ?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=$item["image"]; ?>" width="350" height="260" alt="<?=strip_tags($item["name"]); ?>">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=strip_tags($item["category"]); ?></span>
                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$item["id"];?>"><?=strip_tags($item["name"]); ?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?=show_price($item["price"]); ?><!--Предыдущий способ вывода символа рубля<b class="rub">р</b>--></span>
                    </div>
                    <div class="lot__timer timer">
                        <?=get_time_of_end_lot($item["time"]);?>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
