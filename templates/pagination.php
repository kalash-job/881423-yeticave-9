<?php if ($pages_count > 1): ?>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a<?php if ($current_page !== 1): ?>
                    href="/<?= $part_of_path; ?>page=<?= $current_page - 1; ?><?php endif; ?>">Назад</a></li>
        <?php foreach ($pages as $page): ?>
            <li class="pagination-item<?php if ($page === $current_page): ?> pagination-item-active<?php endif; ?>">
                <a href="/<?= $part_of_path; ?>page=<?= $page; ?>"><?= $page; ?></a></li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next"><a<?php if ($current_page < $pages_count): ?>
                    href="/<?= $part_of_path; ?>page=<?= $current_page + 1; ?><?php endif; ?>">Вперед</a></li>
    </ul>
<?php endif; ?>
<?php if (isset($_GET['search'])): ?></div><?php endif; ?>
