<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($users_bids as $users_bids_option): ?>
            <!--подготовка классов и подписей для форматирования столбца Время до окончания лота-->
            <?php if (isset($users_bids_option['winner_id']) && $users_bids_option['winner_id'] === $users_bids_option['user_id']) {
                $value = ' rates__item--win';
                $contacts = '<p>' . htmlspecialchars($users_bids_option['contact']) . '</p>';
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
            /*подготовка различного формата записи времени и даты для столбца "Время ставки"*/
            $time_of_bid = get_format_time_of_bid($users_bids_option);
            ?>
            <tr class="rates__item<?= $value; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="../<?= $users_bids_option['url']; ?>" width="54" height="40"
                             alt="<?= htmlspecialchars($users_bids_option['name']); ?>">
                    </div>
                    <div>
                        <h3 class="rates__title"><a
                                    href="lot.php?id=<?= $users_bids_option['lot_id']; ?>"><?= htmlspecialchars($users_bids_option['name']); ?></a>
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