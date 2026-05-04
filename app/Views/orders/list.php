<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>My Orders</h3>
        <div>
            <?php if (in_roles('Customer')): ?>
                <a href="<?= base_url('orders/create') ?>" class="btn btn-primary">Create Order</a>
            <?php elseif (in_roles('Tailor')): ?>
                <a href="<?= base_url('orders/bid_list') ?>" class="btn btn-warning">Incoming Requests</a>
            <?php endif; ?>
        </div>
    </div>
    <table id="orderListTable" class="table table-bordered bg-white shadow-sm">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Type</th>
                <th>Material</th>
                <th>Expected Date</th>
                <th>Appointment Date</th>
                <th>Completion Date</th>
                <th>Pickup Date</th>
                <th>Tailor</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach($orders as $o): ?>
                    <tr>
                        <td><?= isset($o['id']) ? '#' . esc($o['id']) : '-' ?></td>
                        <td><?= isset($o['garment_type']) ? esc($o['garment_type']) : '-' ?></td>
                        <td><?= isset($o['material']) ? esc($o['material']) : '-' ?></td>
                        <td><?= isset($o['expected_date']) && $o['expected_date'] ? esc($o['expected_date']) : '-' ?></td>
                        <td><?= !empty($o['appointment_date']) ? date('d M Y, h:i A', strtotime($o['appointment_date'])) : '<i>Awaiting Appointment</i>' ?></td>
                        <td><?= $o['completion_date'] ?? '-' ?></td>
                        <td><?= !empty($o['pickup_date']) ? date('d M Y, h:i A', strtotime($o['pickup_date'])) : '<i>Not picked up yet</i>' ?></td>
                        <td><?= !empty($o['tailor_name']) ? esc($o['tailor_name']) : '<i>Awaiting Bids</i>' ?></td>
                        <td>
                            <span class="badge bg-<?= (isset($o['status']) && $o['status']=='Need Action'?'warning':'info') ?>">
                                <?= isset($o['status']) ? esc($o['status']) : '-' ?>
                            </span>
                        </td>
                        <td>
                            <?php if (isset($o['status']) && $o['status'] == 'Need Action'): ?>
                                <a href="<?= base_url('orders/view_bids/' . $o['id']) ?>"
                                   class="badge bg-warning text-dark text-decoration-none shadow-sm">
                                    <i class="bi bi-exclamation-circle me-1"></i> Need Action
                                </a>
                            <?php else: ?>
                                <span class="badge bg-info"><?= isset($o['status']) ? esc($o['status']) : '-' ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        // 1. Initialize the table
        var table = $('#orderListTable').DataTable({
            searching: true,
            language: {
                search: "Keyword:",
                searchPlaceholder: "Type keyword..."
            }
        });

        // 2. Check the URL for the order_id parameter
        var urlParams = new URLSearchParams(window.location.search);
        var orderId = urlParams.get('order_id');

        // 3. If it exists, tell DataTable to search for that ID automatically
        if (orderId) {
            // We add the # prefix because your IDs in the table look like #11
            table.search('#' + orderId).draw();
        }
    });
</script>
<?= $this->endSection() ?>