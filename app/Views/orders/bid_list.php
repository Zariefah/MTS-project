<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <h3>Incoming Orders & Bids</h3>
    <table id="bidTable" class="table table-bordered bg-white shadow-sm">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Order ID</th>
                <th>Type</th>
                <th>Material</th>
                <th>Region</th>
                <th>Expected Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
            <tr>
                <td><a href="<?= base_url('orders/customer_details/'.$o['id']) ?>" class="fw-bold text-decoration-none"><?= $o['customer_name'] ?></a></td>
                <td>#<?= $o['id'] ?></td>
                <td><?= $o['garment_type'] ?></td>
                <td><?= $o['material'] ?></td>
                <td><?= esc($o['customer_region'] ?? '') ?: '—' ?></td>
                <td><?= $o['expected_date'] ?></td>
                <td>
                    <?php if($o['preferred_tailor_id'] == user_id()): ?>
                        <a href="<?= base_url('orders/customer_details/'.$o['id']) ?>" class="btn btn-sm btn-success">Accept Request</a>
                    <?php else: ?>
                        <a href="<?= base_url('orders/customer_details/'.$o['id']) ?>" class="btn btn-sm btn-primary">Bid Order</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#bidTable').DataTable({
            searching: true,
            language: {
                search: "Keyword:",
                searchPlaceholder: "Type keyword..."
            }
        });
    });
</script>
<?= $this->endSection() ?>
