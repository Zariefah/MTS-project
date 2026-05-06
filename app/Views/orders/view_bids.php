<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>
<div class="container py-4">
    <h4>Tailors Bidding for Order #<?= $order['id'] ?></h4>
    <p class="text-muted"><?= $order['garment_type'] ?> (<?= $order['material'] ?>)</p>

    <?php
    $designImages = [];
    if (!empty($order['design_images'])) {
        $designImages = json_decode($order['design_images'], true) ?: [];
    }
    $materialImages = [];
    if (!empty($order['material_images'])) {
        $materialImages = json_decode($order['material_images'], true) ?: [];
    }
    ?>

    <?php if (!empty($designImages) || !empty($materialImages)): ?>
        <div class="mb-3">
            <?php if (!empty($designImages)): ?>
                <div class="fw-bold mb-1">Design Photos</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($designImages as $img): ?>
                        <a href="<?= base_url('upload/orders/' . (int)$order['id'] . '/design/' . rawurlencode($img)) ?>" target="_blank">
                            <img
                                src="<?= base_url('upload/orders/' . (int)$order['id'] . '/design/' . rawurlencode($img)) ?>"
                                style="width:72px;height:72px;object-fit:cover;border-radius:10px"
                                class="border bg-white"
                            >
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($materialImages)): ?>
                <div class="fw-bold mt-3 mb-1">Material Photos</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($materialImages as $img): ?>
                        <a href="<?= base_url('upload/orders/' . (int)$order['id'] . '/material/' . rawurlencode($img)) ?>" target="_blank">
                            <img
                                src="<?= base_url('upload/orders/' . (int)$order['id'] . '/material/' . rawurlencode($img)) ?>"
                                style="width:72px;height:72px;object-fit:cover;border-radius:10px"
                                class="border bg-white"
                            >
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach($bids as $b): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-0">
                        <a href="<?= base_url('orders/tailor_details/' . (int) $b['tailor_id'] . '?from=bids&order_id=' . $order['id'] . '&bid_id=') ?>" class="fw-semibold text-decoration-none">
                            <?= esc($b['company_name']) ?>
                        </a>
                    </h5>
                    <p class="card-text small">
                        <strong>Proposed Appointment:</strong><br>
                        Date: <?= $b['offered_date'] ?><br>
                        Time: <?= $b['offered_time'] ?>
                    </p>
                    <form action="<?= base_url('orders/accept_bid') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="bid_id" value="<?= $b['id'] ?>">
                        <button type="submit" class="btn btn-success w-100">Accept This Tailor</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
