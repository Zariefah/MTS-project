<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<div class="container">
  <?= view('partials/alert') ?>

  <div class="card">
    <div class="card-body">
      <h3 class="mb-1">Dashboard</h3>
      <div class="text-body-secondary mb-3">You’re logged in, but no dedicated dashboard is configured for your role.</div>

      <div class="p-3 border rounded">
        <div class="fw-bold mb-1">Account</div>
        <div class="small text-body-secondary">
          Username: <?= esc($user?->username ?? '-') ?><br>
          Email: <?= esc($user?->email ?? '-') ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

