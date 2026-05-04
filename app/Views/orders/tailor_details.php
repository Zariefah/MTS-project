<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h3 class="mb-1"><?= esc($profile['organization'] ?? $user->username) ?></h3>
            <p class="text-muted small mb-0">Tailor profile (read-only)</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back();">
                <i class="bi bi-arrow-left"></i> Back
            </button>
            <?php 
                // Get the source and ID data from the URL
                $from = service('request')->getGet('from');
                $order_id = service('request')->getGet('order_id');
                $bid_id = service('request')->getGet('bid_id');
            ?>

            <div>
                <?php if ($from === 'bids'): ?>
                    <form action="<?= base_url('orders/accept_bid') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= esc($order_id) ?>">
                        <input type="hidden" name="bid_id" value="<?= esc($bid_id) ?>">
                        <button type="submit" class="btn btn-success ">
                            Accept This Tailor
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?= base_url('orders/create/' . $user->id) ?>" class="btn btn-primary">
                        Ask for appointment
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Photo</div>
                <div class="card-body text-center">
                    <img src="<?= esc($displayAvatar) ?>" alt="" class="rounded img-fluid" style="max-width: 180px;" width="180" height="180">
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Personal &amp; business</strong>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= esc($user->email) ?></dd>
                        <dt class="col-sm-4">Username</dt>
                        <dd class="col-sm-8"><?= esc($user->username) ?></dd>
                        <dt class="col-sm-4">Shop name</dt>
                        <dd class="col-sm-8"><?= esc($profile['organization'] ?? '—') ?></dd>
                        <dt class="col-sm-4">SSM registration</dt>
                        <dd class="col-sm-8"><?= esc($user->ssm_registration ?? '—') ?></dd>
                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8"><?= nl2br(esc($user->address ?? '')) ?: '—' ?></dd>
                        <dt class="col-sm-4">Region</dt>
                        <dd class="col-sm-8"><?= esc($user->region ?? '—') ?></dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Contact</strong>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Office phone</dt>
                        <dd class="col-sm-8"><?= esc($profile['phone'] ?? '—') ?></dd>
                        <dt class="col-sm-4">Mobile</dt>
                        <dd class="col-sm-8"><?= esc($profile['mobile'] ?? '—') ?></dd>
                        <dt class="col-sm-4">Facebook</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($profile['facebook'])): ?>
                                <a href="<?= esc($profile['facebook']) ?>" target="_blank" rel="noopener noreferrer"><?= esc($profile['facebook']) ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </dd>
                        <dt class="col-sm-4">Instagram</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($profile['instagram'])): ?>
                                <a href="<?= esc($profile['instagram']) ?>" target="_blank" rel="noopener noreferrer"><?= esc($profile['instagram']) ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Specialties</strong>
                </div>
                <div class="card-body">
                    <p class="fw-semibold mb-2">Garment types</p>
                    <p class="mb-4"><?= !empty($profile['tailor_type']) ? esc($profile['tailor_type']) : '—' ?></p>
                    <p class="fw-semibold mb-2">Materials</p>
                    <p class="mb-0"><?= !empty($profile['tailor_material']) ? esc($profile['tailor_material']) : '—' ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
