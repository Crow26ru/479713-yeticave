<?=$categories_list;?>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=$search_str;?></span>»</h2>
        <ul class="lots__list">
            <?php foreach($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$lot['image'];?>" width="350" height="260" alt="<?=$lot['category'];?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$lot['category'];?></span>
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
      <!-- Пагинацию вынести потом отдельным шаблоном -->
      <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
      </ul>
</div>