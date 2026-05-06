<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<div class="container">
  <?= view('partials/alert') ?>

  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-0">Customer Dashboard</h3>
      <div class="text-body-secondary">Overview of your orders, appointments, and completeness checks.</div>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
      <a class="btn btn-outline-primary" href="<?= base_url('orders/create') ?>"><i class="bi bi-plus-square"></i> New order</a>
      <a class="btn btn-primary" href="<?= base_url('orders') ?>"><i class="bi bi-list-check"></i> My orders</a>
    </div>
  </div>

  <?php if (!$profileComplete || !$measurementsComplete): ?>
    <div class="alert alert-warning d-flex align-items-start gap-2">
      <i class="bi bi-exclamation-triangle-fill mt-1"></i>
      <div>
        <div class="fw-bold">Action required</div>
        <div class="small">
          <?php if (!$profileComplete): ?>- Please complete your profile (address + region). <a href="<?= base_url('users/profile/' . user_id()) ?>">Update profile</a><br><?php endif; ?>
          <?php if (!$measurementsComplete): ?>- Please complete your measurements to speed up tailor matching. <a href="<?= base_url('measurements') ?>">Update measurements</a><?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-body-secondary small">Total orders</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['totalOrders'] ?? 0) ?></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-warning-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Pending / Need Action</div>
          <div class="display-6 fw-semibold">
            <?= (int)($kpis['pending'] ?? 0) + (int)($kpis['needAction'] ?? 0) ?>
          </div>
          <div class="small text-body-secondary">
            Pending: <?= (int)($kpis['pending'] ?? 0) ?> · Need Action: <?= (int)($kpis['needAction'] ?? 0) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-primary-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Accepted</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['accepted'] ?? 0) ?></div>
          <div class="small text-body-secondary">Appointment set or tailor accepted your request.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-success-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Completed / Picked up</div>
          <div class="display-6 fw-semibold">
            <?= (int)($kpis['completed'] ?? 0) ?> / <?= (int)($kpis['pickedUp'] ?? 0) ?>
          </div>
          <div class="small text-body-secondary">Completed orders and confirmed pickups.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-lg-5">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Upcoming appointment</div>
            <a class="small" href="<?= base_url('orders') ?>">View all</a>
          </div>

          <?php if (!empty($upcomingAppointment)): ?>
            <div class="p-3 border rounded">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold">Order #<?= (int)$upcomingAppointment['id'] ?></div>
                <span class="badge text-bg-primary">Upcoming</span>
              </div>
              <div class="text-body-secondary small mb-2">
                <?= esc($upcomingAppointment['garment_type'] ?? '-') ?> · <?= esc($upcomingAppointment['material'] ?? '-') ?>
              </div>
              <div>
                <i class="bi bi-calendar-event"></i>
                <span class="fw-semibold"><?= esc((string)($upcomingAppointment['appointment_date'] ?? '')) ?></span>
              </div>
              <div class="small text-body-secondary">
                Tailor: <?= esc($upcomingAppointment['tailor_name'] ?? 'TBA') ?>
              </div>
            </div>
          <?php else: ?>
            <div class="text-body-secondary">No upcoming appointment found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-7">
      <div class="card h-100">
        <div class="card-body">
          <div class="fw-bold mb-2">Orders by status</div>
          <div style="height: 260px;">
            <canvas id="customerStatusChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="fw-bold mb-2">Orders created (last 6 months)</div>
          <div style="height: 260px;">
            <canvas id="customerPerMonthChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Recent orders</div>
            <a class="small" href="<?= base_url('orders') ?>">Open</a>
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Tailor</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentOrders)): foreach ($recentOrders as $o): ?>
                  <tr>
                    <td class="fw-semibold"><?= (int)($o['id'] ?? 0) ?></td>
                    <td><?= esc($o['garment_type'] ?? '-') ?></td>
                    <td><span class="badge text-bg-secondary"><?= esc($o['status'] ?? '-') ?></span></td>
                    <td><?= esc($o['tailor_name'] ?? 'TBA') ?></td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="4" class="text-body-secondary">No orders yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    const statusLabels = <?= json_encode($chart['statusLabels'] ?? []) ?>;
    const statusData = <?= json_encode($chart['statusData'] ?? []) ?>;
    const perMonthLabels = <?= json_encode($chart['perMonthLabels'] ?? []) ?>;
    const perMonthData = <?= json_encode($chart['perMonthData'] ?? []) ?>;

    const elStatus = document.getElementById('customerStatusChart');
    if (elStatus) {
      new Chart(elStatus, {
        type: 'doughnut',
        data: {
          labels: statusLabels,
          datasets: [{
            data: statusData,
            backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#6c757d', '#dc3545', '#20c997'],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } }
        }
      });
    }

    const elMonth = document.getElementById('customerPerMonthChart');
    if (elMonth) {
      new Chart(elMonth, {
        type: 'line',
        data: {
          labels: perMonthLabels,
          datasets: [{
            label: 'Orders',
            data: perMonthData,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.15)',
            fill: true,
            tension: 0.35
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
          plugins: { legend: { display: false } }
        }
      });
    }
  })();
</script>

<?= $this->endSection() ?>

