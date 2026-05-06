<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>
<!--  this one for bidding -->
<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customer Details & Order Information</h5>
            <a href="<?= base_url('orders/bid_list') ?>" class="btn btn-sm btn-outline-light">Back to List</a>
        </div>
        
        <div class="card-body">
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
                                <tr><td><strong>Phone:</strong></td><td><?= esc($profile['mobile'] ?? '+6017-4700724') ?></td></tr>
                                <tr><td><strong>Region:</strong></td><td><?= $customer->region ?></td></tr>
                                <tr><td><strong>Address:</strong></td><td><?= $customer->address ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-success border-bottom pb-2">Order Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td><strong>Garment Type:</strong></td><td><span class="badge bg-secondary"><?= $order['garment_type'] ?></span></td></tr>
                                <tr><td><strong>Material:</strong></td><td><?= $order['material'] ?></td></tr>
                                <tr>
                                    <td><strong>Design Photos:</strong></td>
                                    <td>
                                        <?php if (!empty($designImages)): ?>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($designImages as $img): ?>
                                                    <a href="<?= base_url('upload/orders/' . (int) $order['id'] . '/design/' . rawurlencode($img)) ?>" target="_blank">
                                                        <img
                                                            src="<?= base_url('upload/orders/' . (int) $order['id'] . '/design/' . rawurlencode($img)) ?>"
                                                            style="width:72px;height:72px;object-fit:cover;border-radius:10px"
                                                            class="border bg-white"
                                                        />
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-body-secondary">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Material Photos:</strong></td>
                                    <td>
                                        <?php if (!empty($materialImages)): ?>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($materialImages as $img): ?>
                                                    <a href="<?= base_url('upload/orders/' . (int) $order['id'] . '/material/' . rawurlencode($img)) ?>" target="_blank">
                                                        <img
                                                            src="<?= base_url('upload/orders/' . (int) $order['id'] . '/material/' . rawurlencode($img)) ?>"
                                                            style="width:72px;height:72px;object-fit:cover;border-radius:10px"
                                                            class="border bg-white"
                                                        />
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-body-secondary">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><td><strong>Expected Date:</strong></td><td><?= $order['expected_date'] ?></td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge bg-info"><?= $order['status'] ?></span></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="measurement-pane" role="tabpanel" aria-labelledby="measurement-tab">
                    <?php if ($measurements): ?>
                        <input type="hidden" id="measurementUnit" value="in">
                        <div class="alert alert-light border small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Display unit:
                                <span class="fw-semibold" id="unitLabelTop"></span>
                        </div>
                        
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="text-body-secondary small">
                            </div>
                            <div class="btn-group" role="group" aria-label="Measurement unit switch">
                                <button type="button" class="btn btn-outline-dark" id="btnUnitIn">Inch (in)</button>
                                <button type="button" class="btn btn-outline-dark" id="btnUnitCm">Centimeter (cm)</button>
                            </div>
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
                            foreach ($m_fields as $key => $label): 
                                $value = $measurements[$key] ?? '0.00';
                            ?>
                                <div class="col-md-4 col-6">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <small class="text-muted d-block"><?= $label ?></small>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" class="form-control form-control-sm" 
                                                   value="<?= $value ?>" readonly 
                                                   data-base-in="<?= $value ?>">
                                            <span class="input-group-text unit-suffix">in</span>
                                        </div>
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

        <div class="alert alert-info border-0 shadow-sm mx-3 mt-0 mb-3">
            <i class="bi bi-eye-fill me-2"></i>
            Please review the <strong>Design Photos</strong> and <strong>Material Photos</strong> above before you submit a bid.
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

<script>
    (function () {
        const IN_TO_CM = 2.54;
        const unitInput = document.getElementById('measurementUnit');
        const labelTop = document.getElementById('unitLabelTop');
        const btnIn = document.getElementById('btnUnitIn');
        const btnCm = document.getElementById('btnUnitCm');
        const fields = Array.from(document.querySelectorAll('input[type="number"][readonly]'));
        const suffixes = Array.from(document.querySelectorAll('.unit-suffix'));

        function round1(n) {
            return Math.round(n * 10) / 10;
        }

        function setActiveButtons(unit) {
            const inActive = unit === 'in';
            btnIn.classList.toggle('btn-dark', inActive);
            btnIn.classList.toggle('btn-outline-dark', !inActive);
            btnCm.classList.toggle('btn-dark', !inActive);
            btnCm.classList.toggle('btn-outline-dark', inActive);
        }

        function updateSuffix(unit) {
            suffixes.forEach(s => s.textContent = unit);
            if (labelTop) labelTop.textContent = unit === 'cm' ? 'Centimeter (cm)' : 'Inch (in)';
        }

        function toDisplayValue(baseIn, unit) {
            const n = parseFloat(baseIn);
            if (!isFinite(n)) return '';
            return unit === 'cm' ? String(round1(n * IN_TO_CM)) : String(round1(n));
        }

        function applyUnit(unit) {
            unitInput.value = unit;
            setActiveButtons(unit);
            updateSuffix(unit);
            fields.forEach(el => {
                const base = el.getAttribute('data-base-in') ?? '0';
                el.value = toDisplayValue(base, unit);
            });
        }

        btnIn?.addEventListener('click', () => applyUnit('in'));
        btnCm?.addEventListener('click', () => applyUnit('cm'));

        // Init from saved preference
        const initial = unitInput.value === 'cm' ? 'cm' : 'in';
        applyUnit(initial);
    })();
</script>

<?= $this->endSection() ?>