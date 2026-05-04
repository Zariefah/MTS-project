<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>
<div class="container py-4">
    <h4>Tailors Bidding for Order #<?= $order['id'] ?></h4>
    <p class="text-muted"><?= $order['garment_type'] ?> (<?= $order['material'] ?>)</p>

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
