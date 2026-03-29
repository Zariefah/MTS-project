<?= view('partials/head') ?>

<!--begin::wrapper-->
<div class="wrapper">

  <!--begin::main-->
  <?= view('partials/navbar') ?>
  <?php if (logged_in()) {
    echo view('partials/sidebar');
    echo '<main id="main" class="px-3 expand">';
    $marginTop = 'mt-8';
  } else {
    echo '<main id="main" class="p-3">';
    $marginTop = 'mt-8';
  } ?>
  <div class="<?= $marginTop ?>">
    <?= $this->renderSection('main') ?>
  </div>

  <?= view('partials/foot') ?>

  </main>
  <!--end::main-->

</div>
<!--end::wrapper-->

<?= view('partials/scripts') ?>
