<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<div class="container">
  <?= view('partials/alert') ?>

  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-0">Tailor Dashboard</h3>
      <div class="text-body-secondary">Your incoming requests, active jobs, and completion trend.</div>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
      <a class="btn btn-outline-primary" href="<?= base_url('orders/bid_list') ?>"><i class="bi bi-clipboard-data"></i> Incoming requests</a>
      <a class="btn btn-primary" href="<?= base_url('orders/my_orders') ?>"><i class="bi bi-list-check"></i> My working jobs</a>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-warning-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Incoming requests</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['incomingRequests'] ?? 0) ?></div>
          <div class="small text-body-secondary">Orders needing your bid/action.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-primary-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Active jobs</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['activeJobs'] ?? 0) ?></div>
          <div class="small text-body-secondary">Assigned jobs not yet picked up.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-success-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Completed this month</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['completedThisMonth'] ?? 0) ?></div>
          <div class="small text-body-secondary">Based on completion date.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-body-secondary small">Customers served</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['customersServed'] ?? 0) ?></div>
          <div class="small text-body-secondary">Distinct customers assigned to you.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-lg-5">
      <div class="card h-100">
        <div class="card-body">
          <div class="fw-bold mb-2">Jobs by status</div>
          <div style="height: 260px;">
            <canvas id="tailorStatusChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-7">
      <div class="card h-100">
        <div class="card-body">
          <div class="fw-bold mb-2">Completed jobs (last 6 months)</div>
          <div style="height: 260px;">
            <canvas id="tailorPerMonthChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-bold">Recently assigned jobs</div>
        <a class="small" href="<?= base_url('orders/my_orders') ?>">Open</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Type</th>
              <th>Status</th>
              <th>Appointment</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recentAssigned)): foreach ($recentAssigned as $o): ?>
              <tr>
                <td class="fw-semibold"><?= (int)($o['id'] ?? 0) ?></td>
                <td><?= esc($o['customer_name'] ?? '-') ?></td>
                <td><?= esc($o['garment_type'] ?? '-') ?></td>
                <td><span class="badge text-bg-secondary"><?= esc($o['status'] ?? '-') ?></span></td>
                <td class="text-body-secondary small"><?= esc($o['appointment_date'] ?? '-') ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="5" class="text-body-secondary">No assigned jobs yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
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

    const elStatus = document.getElementById('tailorStatusChart');
    if (elStatus) {
      new Chart(elStatus, {
        type: 'bar',
        data: {
          labels: statusLabels,
          datasets: [{
            label: 'Jobs',
            data: statusData,
            backgroundColor: 'rgba(13,110,253,0.25)',
            borderColor: '#0d6efd',
            borderWidth: 1,
            borderRadius: 8
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

    const elMonth = document.getElementById('tailorPerMonthChart');
    if (elMonth) {
      new Chart(elMonth, {
        type: 'line',
        data: {
          labels: perMonthLabels,
          datasets: [{
            label: 'Completed',
            data: perMonthData,
            borderColor: '#198754',
            backgroundColor: 'rgba(25,135,84,0.15)',
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

