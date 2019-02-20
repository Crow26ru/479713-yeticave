<!--
    $link      - название страницы;
    $id        - значение GET параметра для вывода контента страницы;
    $paginator - целочисленный массив [1, 2, 3, ..., n] для указания номеров страниц
-->
<ul class="pagination-list">
    <?php if($id === 1): ?>
    <li class="pagination-item pagination-item-prev pagination-item-active"><a>Назад</a></li>

    <?php else: ?>
    <li class="pagination-item pagination-item-prev"><a href="<?=$link;?>?id=<?=($id - 1);?>">Назад</a></li>
    <?php endif; ?>

    <?php foreach($paginator as $item): ?>
    <?php if($active_page === $item): ?>
    <li class="pagination-item pagination-item-active"><a><?=$item;?></a></li>

    <?php else: ?>
    <li class="pagination-item"><a href="<?=$page;?>?id=<?=$id;?>"><?=$item;?></a></li>
    <?php endif; ?>

    <?php endforeach; ?>

    <?php if($id === count($paginator)): ?>
    <li class="pagination-item pagination-item-next pagination-item-active"><a>Вперед</a></li>

    <?php else: ?>
    <li class="pagination-item pagination-item-next"><a href="<?=$link;?>?id=<?=($id + 1);?>">Вперед</a></li>
    <?php endif; ?>
</ul>
