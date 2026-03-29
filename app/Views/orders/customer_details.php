<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customer Details & Order Information</h5>
            <a href="<?= base_url('orders/bid_list') ?>" class="btn btn-sm btn-outline-light">Back to List</a>
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
                                <tr><td><strong>Phone:</strong></td><td><?= $customer->mobile ?? 'N/A' ?></td></tr>
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
                            <i class="bi bi-info-circle me-1"></i> Ukuran dalam unit <strong>Inci (Inch)</strong> .
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
            
            <?php if (!empty($already_bid)): ?>
                <span class="text-muted me-2">You already bid on this order.</span>
                <a href="<?= base_url('orders/my_orders') ?>" class="btn btn-primary">View in My Working Jobs</a>
            <?php elseif ($order['preferred_tailor_id'] == user_id()): ?>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bidModal">Accept & Set Appointment</button>
            <?php else: ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bidModal">Submit Bid</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="bidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('orders/submit_bid') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            
            <div class="modal-header">
                <h5 class="modal-title">Set Appointment Date & Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Sila pilih salah satu tarikh yang ditawarkan oleh pelanggan di bawah:</p>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Date</label>
                    <select name="date_id" class="form-select" required>
                        <option value="">-- Choose Date --</option>
                        <?php foreach ($offered_dates as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= $d['offered_date'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Set Time</label>
                    <input type="time" name="time" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm Selection</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>