<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customer Details & Order Information</h5>
        </div>
        
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="customerTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-pane" type="button" role="tab">
                        <i class="bi bi-person-circle me-1"></i> Personal & Order
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="measurement-tab" data-bs-toggle="tab" data-bs-target="#measurement-pane" type="button" role="tab">
                        <i class="bi bi-rulers me-1"></i> Measurement Data
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="customerTabContent">
                
                <div class="tab-pane fade show active" id="personal-pane" role="tabpanel" aria-labelledby="personal-tab">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2">Customer Profile</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>Username:</strong></td><td><?= $customer->username ?></td></tr>
                                <tr><td><strong>Email:</strong></td><td><?= $customer->email ?></td></tr>
                                <tr><td><strong>Phone:</strong></td><td><?= $customer->mobile ?? '+6017-4700724' ?></td></tr>
                                <tr><td><strong>Region:</strong></td><td><?= $customer->region ?></td></tr>
                                <tr><td><strong>Address:</strong></td><td><?= $customer->address ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-success border-bottom pb-2">Order Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>Garment Type:</strong></td><td><span class="badge bg-secondary"><?= $order['garment_type'] ?></span></td></tr>
                                <tr><td><strong>Material:</strong></td><td><?= $order['material'] ?></td></tr>
                                <tr><td><strong>Expected Date:</strong></td><td><?= $order['expected_date'] ?></td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge bg-info"><?= $order['status'] ?></span></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="measurement-pane" role="tabpanel" aria-labelledby="measurement-tab">
                    <div class="mb-3">
                        <a href="<?= base_url('measurements/' . (int) $customer->id) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit full measurement form
                        </a>
                    </div>
                    <?php if ($measurements): ?>
                        <div class="alert alert-light border small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Ukuran dalam unit <strong>Inci (Inch)</strong>.
                        </div>
                        <div class="row g-3">
                            <?php 
                            $m_fields = [
                                'leher' => 'Leher', 'bahu' => 'Bahu', 'bahu_ke_dada' => 'Bahu ke Dada',
                                'bahu_ke_pinggang' => 'Bahu ke Pinggang', 'lilitan_dada' => 'Lilitan Dada',
                                'pinggang' => 'Pinggang', 'punggung' => 'Punggung', 'bahu_ke_lutut' => 'Bahu ke Lutut',
                                'pinggang_ke_lutut' => 'Pinggang ke Lutut', 'labuh_kain' => 'Labuh Kain',
                                'labuh_tangan' => 'Labuh Tangan', 'lilitan_kekek' => 'Lilitan Kekek', 'lubang_tangan' => 'Lubang Tangan'
                            ];
                            foreach ($m_fields as $key => $label): ?>
                                <div class="col-md-4 col-6">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <small class="text-muted d-block"><?= $label ?></small>
                                        <span class="fw-bold"><?= $measurements[$key] ?? '0.00' ?> in</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Customer ini belum memasukkan data ukuran.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white text-end p-3">
            <button type="button" class="btn btn-secondary me-2" onclick="history.back()">Back</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>