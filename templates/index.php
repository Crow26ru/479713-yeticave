<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <!--заполните этот список из массива категорий-->
        <?php
            foreach($categories as $key => $value):
        ?>
        <li class="promo__item promo__item--boards">
            <a class="promo__link" href="pages/all-lots.html"><?=$categories[$key];?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <!--заполните этот список из массива с товарами-->
        <?php
            foreach($products as $position => $item):
        ?>
        <li class="lots__item lot">
            <?php
            // Реализация защиты от XSS-атаки
            // Вырезаем все теги из каждого элемента лота, которые можем получить от пользователя
                foreach($item as $item_element) {
                    $item_element = strip_tags($item_element);
                }
            ?>
            <div class="lot__image">
                <img src="<?=$item["image"]; ?>" width="350" height="260" alt="<?=$item["name"]; ?>">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=$item["category"]; ?></span>
                <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=$item["name"]; ?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?=show_price($item["price"]); ?><!--Предыдущий способ вывода символа рубля<b class="rub">р</b>--></span>
                    </div>
                    <div class="lot__timer timer">
                        12:23
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
