<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $categories_option): ?>
            <li class="nav__item<?php if (isset($_GET['category']) && (int)$_GET['category'] === $categories_option["id"]): ?> nav__item--current<?php endif; ?>">
                <a href="/all-lots.php?category=<?= $categories_option["id"]; ?>"><?= $categories_option["name"]; ?></a>
            </li>
        <?php endforeach ?>
    </ul>
</nav>