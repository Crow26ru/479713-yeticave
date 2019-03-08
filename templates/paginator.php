<ul class="pagination-list">
    <?php if($active_page !== 1): ?>
    <li class="pagination-item pagination-item-prev"><a href="<?=$link . ($active_page - 1) . '&id=' . $id;?>">Назад</a></li>
    <?php endif; ?>

    <?php foreach($paginator as $item): ?>
    <li class="pagination-item <?=$active_page === $item ? 'pagination-item-active' : '';?>">
        <a <?=$active_page !== $item ? 'href="' . ($link . $item) . '&id=' . $id . '"' : '';?>><?=$item;?></a>
    </li>

    <?php endforeach; ?>

    <?php if($active_page !== $total_pages) : ?>
    <li class="pagination-item pagination-item-next"><a href="<?=$link . ($active_page + 1) . '&id=' . $id;?>">Вперед</a></li>
    <?php endif; ?>
</ul>
