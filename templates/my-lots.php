<?=$categories_list;?>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
    <?php foreach($rates as $rate): ?>
        <tr class="rates__item">
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
                <div class="timer timer--finishing"><?=get_time_of_end_lot($lot['time'], true);?></div> <!-- Формат hh:mm:ss -->
            </td>
            <td class="rates__price">
                <?=show_price($rate['cost']);?>
            </td>
            <td class="rates__time">
            <?=show_user_frendly_time($rate['cost']);?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</section>
