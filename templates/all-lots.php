<div class="container">
    <section class="lots">
        <h2>Все лоты в категории <span>«<?= $category_name; ?>»</span></h2>
        <?php if ($items !== null): ?>
            <ul class="lots__list">
                <?php foreach ($items as $value): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?= $value["url"]; ?>" width="350" height="260" alt="">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?= htmlspecialchars($value["category"]); ?></span>
                            <h3 class="lot__title"><a class="text-link"
                                                      href="/lot.php?id=<?= $value["id"]; ?>"><?= htmlspecialchars($value["name"]); ?></a>
                            </h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= format_cost($value["price"]); ?></span>
                                </div>
                                <div class="lot__timer timer<?= color_hour_to_closing_date((int)$value["timestamp_to_clos_date"]); ?>">
                                    <?= time_to_closing_date((int)$value["timestamp_to_clos_date"]); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php else: ?>
            <p>К сожалению, в данной категории нет действующих лотов</p>
        <?php endif; ?>
    </section>