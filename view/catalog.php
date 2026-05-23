<?php require BASE_PATH . '/view/layout/header.php';
require_once BASE_PATH . '/view/ItemView.php';
?>

<div class="section catalog page">
  <div class="wrapper">

    <h1>
      <?php
      $title = $pageTitle;

      if (!empty($search)) {
        $title = 'Search results for "' . htmlspecialchars($search) . '"';
      }

      if (!empty($section)) {
        $title .= ' in ' . ucfirst($section);
      }

      echo $title;
      ?>
    </h1>

    <?php if (empty($catalog)): ?>

      <p>No items were found matching that search term.</p>

      <p>
        Search again or
        <a href="index.php?page=catalog">Browse the Full Catalog.</a>
      </p>

    <?php else: ?>

      <?php require BASE_PATH . '/view/partials/pagination.php'; ?>

      <ul class="catalog">
        <?php foreach ($catalog as $item): ?>
          <?= ItemView::render($item); ?>
        <?php endforeach; ?>
      </ul>

      <?php require BASE_PATH . '/view/partials/pagination.php'; ?>

    <?php endif; ?>

  </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>