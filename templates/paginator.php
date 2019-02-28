<!--
    $link      - название страницы;
    $id        - значение GET параметра для вывода контента страницы;
    $paginator - целочисленный массив [1, 2, 3, ..., n] для указания номеров страниц
-->
<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev <?=$id === 1 ? 'pagination-item-active' : '';?>"><a <?=$id !== 1 ? '\'href="' . <?=$first_page> . '"' : '';?>>Назад</a></li>

    <?php foreach($paginator as $item): ?>
    <li class="pagination-item <?=$active_page === $item ? 'pagination-item-active' : '';?>"><a <?=$active_page !== $item ? '\'href="' . <?=$page;?> . '?id=' . <?=$id;?> . '"' : '';?>><?=$item;?></a></li>

    <?php endforeach; ?>

    <li class="pagination-item pagination-item-next <?=$id === count($paginator) ? 'pagination-item-active' : '';?>"><a <?=$id !== count($paginator) ? '\'href="' . <?=$last_page> . '"' : '';?>>Вперед</a></li>
</ul>
