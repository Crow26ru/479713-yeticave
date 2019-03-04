<?=$categories_list;?>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
    <?php foreach($rates as $rate): ?>
        <tr class="rates__item <?=$rate['is_win'] ? 'rates__item--win' : '';?> <?=rate['is_end'] ? 'rates__item--end' : '';?>">
            <td class="rates__info">
                <div class="rates__img">
                    <img src="<?=$rate['image'];?>" width="54" height="40" alt="<?=$rate['category'];?>">
                </div>
                <h3 class="rates__title"><a href="lot.php?id=<?=$rate['id'];?>"><?=strip_tags($rate['name']);?></a></h3>
            </td>
            <td class="rates__category">
                <?=$rate['category'];?>
            </td>
            <td class="rates__timer">
                <div class="timer <?=$rate['is_finishing'] ? 'timer--finishing' : '';?> <?=$rate['is_end'] ? 'timer--end' : '';?> <?=rate['is_win'] ? 'timer--win' : '';?>">
                    <?php if(rate['is_win']): ?>
                    Ставка выйграла
                    <?php else if(rate['is_end']): ?>
                    Торги окончены
                    <?php else {$rate['time'];} ?>
                    <?php endif; ?>
                </div>
            </td>
            <td class="rates__price">
                <?=show_price($rate['rate']);?>
            </td>
            <td class="rates__time">
            <?=show_user_frendly_time($rate['date_add']);?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</section>
