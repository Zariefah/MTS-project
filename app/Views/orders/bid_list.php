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
                <th>Design Photos</th>
                <th>Material Photos</th>
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
                <td>
                    <?php
                    $designImages = [];
                    if (!empty($o['design_images'])) {
                        $designImages = json_decode($o['design_images'], true) ?: [];
                    }
                    ?>
                    <?php if (!empty($designImages)): ?>
                        <a href="<?= base_url('orders/customer_details/'.$o['id']) ?>" title="View design">
                            <img
                                src="<?= base_url('upload/orders/' . (int)$o['id'] . '/design/' . rawurlencode($designImages[0])) ?>"
                                style="width:56px;height:56px;object-fit:cover;border-radius:10px"
                                class="border bg-white"
                            >
                        </a>
                    <?php else: ?>
                        <span class="text-body-secondary">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $materialImages = [];
                    if (!empty($o['material_images'])) {
                        $materialImages = json_decode($o['material_images'], true) ?: [];
                    }
                    ?>
                    <?php if (!empty($materialImages)): ?>
                        <a href="<?= base_url('orders/customer_details/'.$o['id']) ?>" title="View material photos">
                            <img
                                src="<?= base_url('upload/orders/' . (int)$o['id'] . '/material/' . rawurlencode($materialImages[0])) ?>"
                                style="width:56px;height:56px;object-fit:cover;border-radius:10px"
                                class="border bg-white"
                            >
                        </a>
                    <?php else: ?>
                        <span class="text-body-secondary">—</span>
                    <?php endif; ?>
                </td>
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
