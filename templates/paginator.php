<!--
    $link         - название страницы;
    $active_page  - значение GET параметра для вывода контента страницы;
    $paginator    - целочисленный массив [1, 2, 3, ..., n] для указания номеров страниц
    $total_pages  - страниц всего
-->
<ul class="pagination-list">
    <?php if($active_page !== 1): ?>
    <li class="pagination-item pagination-item-prev"><a <?=$link . ($active_page - 1);?>>Назад</a></li>
    <?php endif; ?>

    <?php foreach($paginator as $item): ?>
    <li class="pagination-item <?=$active_page === $item ? 'pagination-item-active' : '';?>"><a <?=$active_page !== $item ? ($link . $item) : '';?>><?=$item;?></a></li>

    <?php endforeach; ?>

    <?php if($active_page !== $total_pages) : ?>
    <li class="pagination-item pagination-item-next"><a <?=$link . ($active_page + 1);?>>Вперед</a></li>
    <?php endif; ?>
</ul>
