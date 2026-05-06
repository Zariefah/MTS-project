<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<div class="container">
  <?= view('partials/alert') ?>

  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-0">Admin Dashboard</h3>
      <div class="text-body-secondary">System overview: users, orders, and recent activity.</div>
    </div>
    <div class="mt-2 mt-md-0 d-flex gap-2">
      <a class="btn btn-outline-primary" href="<?= base_url('users') ?>"><i class="bi bi-people-fill"></i> Users</a>
      <a class="btn btn-outline-secondary" href="<?= base_url('roles') ?>"><i class="bi bi-person-circle"></i> Roles</a>
      <a class="btn btn-primary" href="<?= base_url('log') ?>"><i class="bi bi-card-list"></i> Log</a>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-body-secondary small">Total users</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['totalUsers'] ?? 0) ?></div>
          <div class="small text-body-secondary">Active: <?= (int)($kpis['activeUsers'] ?? 0) ?></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100 border-primary-subtle">
        <div class="card-body">
          <div class="text-body-secondary small">Total orders</div>
          <div class="display-6 fw-semibold"><?= (int)($kpis['totalOrders'] ?? 0) ?></div>
          <div class="small text-body-secondary">Pending: <?= (int)($kpis['pendingOrders'] ?? 0) ?></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-body-secondary small">Customers</div>
          <div class="display-6 fw-semibold"><?= (int)($roleCounts['Customer'] ?? 0) ?></div>
          <div class="small text-body-secondary">Role membership count.</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-body-secondary small">Tailors</div>
          <div class="display-6 fw-semibold"><?= (int)($roleCounts['Tailor'] ?? 0) ?></div>
          <div class="small text-body-secondary">Role membership count.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-lg-5">
      <div class="card h-100">
        <div class="card-body">
          <div class="fw-bold mb-2">Orders by status</div>
          <div style="height: 260px;">
            <canvas id="adminOrderStatusChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-7">
      <div class="card h-100">
        <div class="card-body">
          <div class="fw-bold mb-2">Orders created (last 6 months)</div>
          <div style="height: 260px;">
            <canvas id="adminPerMonthChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Recent users</div>
            <a class="small" href="<?= base_url('users') ?>">Open</a>
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Active</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentUsers)): foreach ($recentUsers as $u): ?>
                  <tr>
                    <td class="fw-semibold"><?= (int)($u->id ?? 0) ?></td>
                    <td><?= esc($u->username ?? '-') ?></td>
                    <td class="text-body-secondary small"><?= esc($u->email ?? '-') ?></td>
                    <td><?= ((int)($u->active ?? 0) === 1) ? '<span class="badge text-bg-success">Yes</span>' : '<span class="badge text-bg-secondary">No</span>' ?></td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="4" class="text-body-secondary">No users found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Recent log events</div>
            <a class="small" href="<?= base_url('log') ?>">Open</a>
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>User</th>
                  <th>Event</th>
                  <th>At</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentLogs)): foreach ($recentLogs as $l): ?>
                  <tr>
                    <td><span class="badge text-bg-secondary"><?= esc($l['type'] ?? '-') ?></span></td>
                    <td class="text-body-secondary small"><?= esc($l['user'] ?? '-') ?></td>
                    <td><?= esc($l['event'] ?? '-') ?></td>
                    <td class="text-body-secondary small"><?= esc($l['created_at'] ?? '-') ?></td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="4" class="text-body-secondary">No log entries.</td></tr>
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
    const statusLabels = <?= json_encode($chart['orderStatusLabels'] ?? []) ?>;
    const statusData = <?= json_encode($chart['orderStatusData'] ?? []) ?>;
    const perMonthLabels = <?= json_encode($chart['perMonthLabels'] ?? []) ?>;
    const perMonthData = <?= json_encode($chart['perMonthData'] ?? []) ?>;

    const elStatus = document.getElementById('adminOrderStatusChart');
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

    const elMonth = document.getElementById('adminPerMonthChart');
    if (elMonth) {
      new Chart(elMonth, {
        type: 'bar',
        data: {
          labels: perMonthLabels,
          datasets: [{
            label: 'Orders',
            data: perMonthData,
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
  })();
</script>

<?= $this->endSection() ?>

