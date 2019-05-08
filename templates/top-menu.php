<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $categories_option): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?= $categories_option["name"]; ?></a>
            </li>
        <?php endforeach ?>
    </ul>
</nav>