<?php if ($totalPages > 1): ?>
<div class="pagination">
    Pages:
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

        <?php if ($i == $currentPage): ?>
            <span><?= $i ?></span>
        <?php else: ?>
            <?php
                $query = [
                    'page' => 'catalog',
                    'pg'   => $i
                ];
                if (!empty($section)) $query['cat'] = $section;
                if (!empty($search))  $query['s']   = $search;
            ?>
            <a href="index.php?<?= http_build_query($query) ?>">
                <?= $i ?>
            </a>
        <?php endif; ?>

    <?php endfor; ?>
</div>
<?php endif; ?>
