<div class="history">
    <h3>История ставок (<span><?=count($rates);?></span>)</h3>
    <?php if($rates):?>
    <table class="history__list">
        <?php foreach($rates as $item):?>
        <tr class="history__item">
            <td class="history__name"><?=strip_tags($item['name']);?></td>
            <td class="history__price"><?=show_price($item['rate']);?></td>
            <td class="history__time"><?=show_user_frendly_time($item['time']);?></td>
        </tr>
        <?php endforeach; ?>         
    </table>
    <?php endif; ?>
</div>