<section class="lot-item container">
    <h2><?= htmlspecialchars($current_lot["name"]); ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?= $current_lot["url"]; ?>" width="730" height="548"
                     alt="<?= htmlspecialchars($current_lot["name"]); ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= $current_lot["category"]; ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($current_lot["description"]); ?></p>
        </div>
        <div class="lot-item__right">
            <?php if ($new_bid_adding['show_block'] === true): ?>
                <div class="lot-item__state">
                    <div class="lot-item__timer timer<?= color_hour_to_closing_date((int)$current_lot["timestamp_to_clos_date"]); ?>">
                        <?= time_to_closing_date((int)$current_lot["timestamp_to_clos_date"]); ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= format_cost_for_bids_block(htmlspecialchars($current_lot["current_price"])); ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка
                            <span><?= format_cost_for_bids_block(htmlspecialchars($current_lot["min_bid"])); ?> р</span>
                        </div>
                    </div>
                    <form class="lot-item__form" action="<?= "/lot.php?id=" . $_GET['id']; ?>" method="post"
                          autocomplete="off">
                        <p class="lot-item__form-item form__item<?= $new_bid_adding["form_error_class"]; ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost"
                                   placeholder="<?= format_cost_for_bids_block(htmlspecialchars($current_lot["min_bid"])); ?>">
                            <span class="form__error"><?= $new_bid_adding["error_note"]; ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок (<span><?= count($bids_by_lot); ?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bids_by_lot as $lots_bids_option): ?>
                        <?php $time_of_bid = get_format_time_of_bid($lots_bids_option); ?>
                        <tr class="history__item">
                            <td class="history__name"><?= htmlspecialchars($lots_bids_option['name']); ?></td>
                            <td class="history__price"><?= format_cost_for_bids(htmlspecialchars($lots_bids_option['bid_amount'])); ?></td>
                            <td class="history__time"><?= $time_of_bid; ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        </div>
    </div>
</section>