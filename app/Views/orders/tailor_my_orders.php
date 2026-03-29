<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>My Working Jobs</h3>
        <a href="<?= base_url('orders/bid_list') ?>" class="btn btn-warning">Incoming Requests</a>
    </div>

    <table id="tailorMyOrdersTable" class="table table-bordered bg-white shadow-sm">
        <thead class="bg-light">
            <tr>
                <th>Customer</th>
                <th>Order ID</th>
                <th>Type</th>
                <th>Material</th>
                <th>Appointment Date</th>
                <th>Completion Date</th>
                <th>Pickup Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
            <tr>
                <td>
                    <?= $o['customer_name'] ?>
                    <div>
                        <a href="<?= base_url('orders/customer_details2/' . $o['id']) ?>" class="small">
                            See Cust's Info & Measurement
                        </a>
                    </div>
                </td>
                <td>#<?= $o['id'] ?></td>
                <td><?= $o['garment_type'] ?></td>
                <td><?= $o['material'] ?></td>
                <td><?= $o['appointment_date'] ? date('d M Y', strtotime($o['appointment_date'])) : '-' ?></td>
                <td><?= $o['completion_date'] ?? '-' ?></td>
                <td><?= !empty($o['pickup_date']) ? date('d M Y', strtotime($o['pickup_date'])) : '-' ?></td>
                <td>
                    <?php
                    $ds = $o['display_status'] ?? $o['status'];
                    $statusBadgeClass = $ds === 'Completed' ? 'bg-success' : ($ds === 'Pending' ? 'bg-warning text-dark' : 'bg-primary');
                    ?>
                    <span class="badge <?= $statusBadgeClass ?>">
                        <?= esc($ds) ?>
                    </span>
                </td>
                <td>
                    <?php if (empty($o['selected_tailor_id'])): ?>
                        <span class="text-muted small">Awaiting customer to accept your bid.</span>
                    <?php elseif (!empty($o['pickup_date'])): ?>
                        <span class="badge bg-secondary mb-2">Status Locked</span><br>
                        <span class="badge bg-info"><?= esc($o['status']) ?></span><br>
                        <span class="badge bg-success">Pickup Done</span>
                    <?php else: ?>
                        <form action="<?= base_url('orders/update_status') ?>" method="post" class="mb-2">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <div class="input-group input-group-sm">
                                <select name="status" class="form-select">
                                    <option value="Accepted" <?= $o['status'] == 'Accepted' ? 'selected' : '' ?>>Accepted</option>
                                    <option value="In Progress" <?= $o['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= $o['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">Update</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($o['selected_tailor_id']) && empty($o['pickup_date']) && $o['status'] === 'Completed'): ?>
                        <form action="<?= base_url('orders/mark_pickup_done') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pickup_done_<?= $o['id'] ?>" onchange="this.form.submit()">
                                <label class="form-check-label" for="pickup_done_<?= $o['id'] ?>">
                                    Done Pickup
                                </label>
                            </div>
                        </form>
                  
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; if(empty($orders)) echo "<tr><td colspan='9' class='text-center'>You have no active jobs.</td></tr>"; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#tailorMyOrdersTable').DataTable({
            searching: true,
            language: {
                search: "Keyword:",
                searchPlaceholder: "Type keyword..."
            }
        });
    });
</script>
<?= $this->endSection() ?>