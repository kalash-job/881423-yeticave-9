USE yeticave;
INSERT INTO category
    (name, css_class)
VALUES ("Доски и лыжи", "boards"),
       ("Крепления", "attachment"),
       ("Ботинки", "boots"),
       ("Одежда", "clothing"),
       ("Инструменты", "tools"),
       ("Разное", "other");

INSERT INTO user
    (email, name, password, contact)
VALUES ("misha@email", "Михаил", "111111", "+77795484"),
       ("lev@email", "Лев", "222222", "+778995484"),
       ("nikolay@email", "Nikolay", "nnnnnn", "+788554665");

INSERT INTO lot
(name, description, url, price, completion_date, bid_step, category_id, user_id)
VALUES ("2014 Rossignol District Snowboard",
        "The Rossignol District Amptek Snowboard is an user-friendly freestyle board for the aspiring park and pipe riders.",
        "img/lot-1.jpg", 10999, "2019-04-29 05:48:05", 300, 1, 1),
       ("DC Ply Mens 2016/2017 Snowboard",
        "Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.",
        "img/lot-2.jpg", 159999, "2019-04-29 05:48:05", 450, 1, 2),
       ("Крепления Union Contact Pro 2015 года размер L/XL",
        "Легкая и прочная база Duraflex™ CP3 MD из запатентованного нейлонового композита, пяточная дуга из прочного экструдированного алюминия Extruded 3D (она не изменит своей формы вне зависимости от нагрузки на нее), амортизационные подушки Vaporlite и Multidensity Thermoformed EVA Bushing (они отвечают за отзывчивость и гашение вибраций), а также удобный хайбэк СР3, рассчитанный на самые разные стили катания и удобные стрепы Classic Lite и Ultragrip.",
        "img/lot-3.jpg", 8000, "2019-04-28 05:48:05", 200,
        2, 3),
       ("Ботинки для сноуборда DC Mutiny Charocal",
        "Прогрессивный дизайн в классическом силуэте - эти ботинки созданы для комфортного катания и высокой производительности.",
        "img/lot-4.jpg", 10999, "2019-04-29 05:48:05", 500, 3,
        2),
       ("Куртка для сноуборда DC Mutiny Charocal",
        "Наружная часть из мягкого гладкого материала. Водонепроницаемость до 10,000 мм, дышащая отделка до 5000 г/м",
        "img/lot-5.jpg", 7500, "2019-04-29 09:48:05", 250, 4,
        1),
       ("Маска Oakley Canopy",
        "Увеличенный объем линзы и низкий профиль оправы маски Canopy способствуют широкому углу обзора, а специальное противотуманное покрытие поможет ориентироваться в условиях плохой видимости. Технология вентиляции O-Flow Arch и прослойка из микрофлиса сделают покорение горных склонов более комфортным.",
        "img/lot-6.jpg", 5400, "2019-05-28 05:48:05", 100, 6, 2);

INSERT INTO bid
    (date, bid_amount, user_id, lot_id)
VALUES ("2019-04-25 05:55:13", 10999, 1, 1),
       ("2019-04-25 05:59:13", 11299, 2, 1),
       ("2019-04-25 06:01:13", 11599, 3, 1);

/*получить все категории*/
SELECT name
FROM category;

/*получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории*/
SELECT l.name,
       l.price,
       l.url,
       c.name                                                                          AS category_name,
       l.id,
       l.creation_date,
       MAX(GREATEST(COALESCE(l.price, b.bid_amount), COALESCE(b.bid_amount, l.price))) AS current_price
FROM lot l
         LEFT JOIN category c
                   ON l.category_id = c.id
         LEFT JOIN bid b
                   ON l.id = b.lot_id
WHERE l.completion_date > now()
GROUP BY l.id, l.name, l.url, l.price, l.creation_date, c.name
ORDER BY l.creation_date DESC
LIMIT 6;


/*показать лот по его id. Получите также название категории, к которой принадлежит лот*/
SELECT lot.name AS lot_name, category.name AS category_name
FROM lot
         JOIN category
              ON lot.category_id = category.id
WHERE lot.id = 1;

/*обновить название лота по его идентификатору;*/
UPDATE lot
SET name = "2015 Rossignol District Snowboard"
WHERE id = 1;

/*получить список самых свежих ставок для лота по его идентификатору*/
SELECT bid_amount AS last_bets
FROM bid
WHERE lot_id = 1
ORDER BY date DESC
LIMIT 3;
