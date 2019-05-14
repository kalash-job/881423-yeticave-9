<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($users_bids as $users_bids_option): ?>
            <?php if (isset($users_bids_option['winner_id']) && $users_bids_option['winner_id'] === $users_bids_option['user_id']) {
                $value = ' rates__item--win';
                $contacts = '<p>' . $users_bids_option['contact'] . '</p>';
                $class_timer = ' timer--win';
                $note_timer = 'Ставка выиграла';
            } elseif (isset($users_bids_option['timestamp_to_clos_date']) && (int)$users_bids_option["timestamp_to_clos_date"] < 0) {
                $value = ' rates__item--end';
                $contacts = '';
                $class_timer = ' timer--end';
                $note_timer = 'Торги окончены';
            } else {
                $value = '';
                $contacts = '';
                $class_timer = color_hour_to_closing_date((int)$users_bids_option["timestamp_to_clos_date"]);
                $note_timer = $users_bids_option["time_to_clos_date"];
            }
            $timestamp_after_midnight = strtotime("today midnight");
            $hours_ago = (int)((int)$users_bids_option["timestamp_after_bid"] / 3600);
            $minuts_ago = (int)((int)$users_bids_option["timestamp_after_bid"] / 60 - $hours_ago * 60);
            $timestamp_after_yesterday_midnight = strtotime("yesterday midnight");
            if ((int)$users_bids_option["timestamp_after_bid"] < 3600) {
                $time_of_bid = "{$minuts_ago} " . get_noun_plural_form($minuts_ago,
                        'минута', 'минуты', 'минут') . ' назад';
            } elseif ((int)$users_bids_option["timestamp_after_bid"] >= 3600 && ((int)$users_bids_option["timestamp_after_bid"] < 7200 && (int)$users_bids_option["timestamp_bid"] > $timestamp_after_midnight)) {
                $time_of_bid = get_noun_plural_form($hours_ago,
                        'час ', 'часа ', 'часов ') . ($minuts_ago) . " " . get_noun_plural_form($minuts_ago,
                        'минута', 'минуты', 'минут') . ' назад';
            } elseif
            ((int)$users_bids_option["timestamp_after_bid"] >= 3600 && (int)$users_bids_option["timestamp_bid"] > $timestamp_after_midnight) {
                $time_of_bid = "{$hours_ago} " . get_noun_plural_form($hours_ago,
                        'час ', 'часа ', 'часов ') . "{$minuts_ago} " . get_noun_plural_form($minuts_ago,
                        'минута', 'минуты', 'минут') . ' назад';
            } elseif
            ((int)$users_bids_option["timestamp_bid"] >= $timestamp_after_yesterday_midnight && (int)$users_bids_option["timestamp_bid"] < $timestamp_after_midnight) {
                $time_of_bid = "Вчера, в " . $users_bids_option["time_of_bid"];
            } elseif
            ((int)$users_bids_option["timestamp_bid"] < $timestamp_after_yesterday_midnight) {
                $time_of_bid = $users_bids_option["date_time_of_bid"];
            }
            ?>
            <tr class="rates__item<?= $value; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="../<?= $users_bids_option['url']; ?>" width="54" height="40"
                             alt="<?= $users_bids_option['name']; ?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a
                                    href="lot.php?id=<?= $users_bids_option['lot_id']; ?>"><?= $users_bids_option['name']; ?></a>
                        </h3>
                        <?= $contacts; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= $users_bids_option['category']; ?>
                </td>
                <td class="rates__timer">
                    <div class="timer<?= $class_timer; ?>"><?= $note_timer; ?></div>
                </td>
                <td class="rates__price">
                    <?= format_cost_for_bids($users_bids_option["bid_amount"]); ?>
                </td>
                <td class="rates__time">
                    <?= $time_of_bid; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</section>