    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
            горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($categories as $categories_option): ?>
                <li class="promo__item <?= "promo__item--" . $categories_option["css_class"]; ?>">
                    <a class="promo__link"
                       href="/all-lots.php?category=<?= $categories_option["id"]; ?>"><?= $categories_option["name"]; ?></a>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
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
                                <!--Вставка функции format_cost -->
                                <span class="lot__cost"><?= format_cost(htmlspecialchars($value["price"])); ?></span>
                            </div>
                            <div class="lot__timer timer<?= color_hour_to_closing_date((int)$value["timestamp_to_clos_date"]); ?>">
                                <?= time_to_closing_date((int)$value["timestamp_to_clos_date"]); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
