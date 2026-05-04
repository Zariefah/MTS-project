<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="mb-3 d-flex justify-content-end">
        <?php if (in_roles('Customer')): ?>
            <a href="<?= base_url('orders/create') ?>" class="btn btn-primary me-2">Create Order</a>
            <a href="<?= base_url('orders') ?>" class="btn btn-secondary">My Orders</a>
        <?php elseif (in_roles('Tailor')): ?>
            <?php if (!empty($orderId)): ?>
                <a href="<?= base_url('orders/customer_details2/' . (int)$orderId) ?>" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to Order
                </a>
            <?php else: ?>
                <a href="<?= base_url('orders/my_orders') ?>" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to Orders
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold">MEASUREMENT GUIDE</h5>
                <span class="badge bg-primary px-3 py-2">Customer: <?= $targetUser->username ?></span>
            </div>
        </div>
        <div class="card-body bg-light">
            <?php if (in_roles('Tailor') && !empty($tailorCanEditMeasurements)): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
                    <strong>Notice / Perhatian:</strong>
                    Customers may not measure themselves accurately even with the guide. 
                    Confirm values before saving for customer <strong><?= esc($targetUser->username) ?></strong>.
                    
                    <span class="d-block text-muted small mt-1">Pelanggan mungkin tidak mengukur badan sendiri dengan tepat walaupun ada panduan.
                    Pastikan sebelum mengemas kini ukuran pelanggan <strong><?= esc($targetUser->username) ?></strong>.</span>
                </div>
            <?php elseif (in_roles('Tailor') && empty($tailorCanEditMeasurements)): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
                    <strong>Paparan sahaja / View only:</strong>
                    Anda hanya boleh mengemas kini ukuran selepas pesanan dengan pelanggan ini berstatus <strong>Accepted</strong> (atau kemudian: In Progress, Completed).
                    <span class="d-block text-muted small mt-1">You can only update measurements after an order with this customer is <strong>Accepted</strong> (or later: In Progress, Completed).</span>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-5 mb-4 text-center">
                    <div class="sticky-top" style="top: 70px; z-index: 10;">
                        <div class="card bg-white border-0 shadow-sm">
                            <div class="card-body p-2">
                                Make sure the measurements are taken in units <strong>Inch (In)</strong> according to the diagram.<br>
                                <text class="text-muted small">Pastikan ukuran diambil dalam unit <strong>Inci (Inch)</strong> mengikut label rajah.</text>
                            </div>
                        </div>
                        <img src="<?= base_url('images/measurements.png') ?>" class="img-fluid img-thumbnail rounded shadow mt-2" alt="Jemari Guide">
                    </div>
                </div>

                <div class="col-lg-7">
                    <form action="<?= base_url('measurements/save') ?>" method="post"
                        <?php if (in_roles('Tailor') && !empty($tailorCanEditMeasurements)): ?>
                            onsubmit="return confirm(<?= json_encode('Are you sure you want to update customer ' . $targetUser->username . '\'s body measurements?') ?>);"
                        <?php endif; ?>
                    >
                        <?= csrf_field() ?>
                        <?php if (in_roles('Tailor') && !empty($tailorCanEditMeasurements)): ?>
                            <input type="hidden" name="target_user_id" value="<?= (int) $targetUser->id ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <?php
                            $guide = [
                                'A' => ['leher', '<span>Neck </span><span class="text-muted"> (Leher)</span>'], 
                                'B' => ['bahu', '<span>Shoulder </span><span class="text-muted"> (Bahu)</span>'],
                                'C' => ['bahu_ke_dada', '<span>Shoulder to Bust </span><span class="text-muted"> (Bahu ke Dada)</span>'], 
                                'D' => ['bahu_ke_pinggang', '<span>Shoulder to Waist </span><span class="text-muted"> (Bahu ke Pinggang)</span>'],
                                'E' => ['lilitan_dada', '<span>Bust </span><span class="text-muted"> (Lilitan Dada)</span>'], 
                                'F' => ['pinggang', '<span>Waist </span><span class="text-muted"> (Pinggang)</span>'],
                                'G' => ['punggung', '<span>Back </span><span class="text-muted"> (Punggung)</span>'], 
                                'I' => ['bahu_ke_lutut', '<span>Shoulder to Knee </span><span class="text-muted"> (Bahu ke Lutut)</span>'],
                                'J' => ['pinggang_ke_lutut', '<span>Waist to Knee </span><span class="text-muted"> (Pinggang ke Lutut)</span>'], 
                                'K' => ['labuh_kain', '<span>Length of Fabric </span><span class="text-muted"> (Labuh Kain)</span>'],
                                'L' => ['labuh_tangan', '<span>Length of Sleeve </span><span class="text-muted"> (Labuh Tangan)</span>'], 
                                'M' => ['lilitan_kekek', '<span>Cuff </span><span class="text-muted"> (Lilitan Ketiak)</span>'],
                                'N' => ['lubang_tangan', '<span>Cuff Opening </span><span class="text-muted"> (Lubang Tangan)</span>']
                            ];

                            foreach ($guide as $code => $field): ?>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group mb-2">
                                        <label class="form-label font-weight-bold">
                                            <span class="text-primary"><?= $code ?></span> - <?= $field[1] ?>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" name="<?= $field[0] ?>" 
                                                   class="form-control" 
                                                   value="<?= $measurement[$field[0]] ?? '0' ?>"
                                                   <?= (in_roles('Tailor') && empty($tailorCanEditMeasurements)) ? 'readonly' : '' ?>>
                                            <span class="input-group-text bg-white">in</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4">
                            <?php if (in_roles('Customer')): ?>
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm w-100">Submit</button>
                            <?php elseif (in_roles('Tailor') && !empty($tailorCanEditMeasurements)): ?>
                                <button type="submit" class="btn btn-warning btn-lg shadow-sm w-100">Update customer measurements</button>
                            <?php elseif (in_roles('Tailor')): ?>
                                <div class="alert alert-secondary border-0 text-center mb-0">
                                    <i class="bi bi-info-circle me-1"></i> Editing is available after the order status is Accepted.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary border-0 text-center">
                                    <i class="bi bi-info-circle me-1"></i> View only.
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>