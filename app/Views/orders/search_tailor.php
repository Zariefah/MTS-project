<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Create Order with a Tailor</h3>
        <a href="<?= base_url('orders/create') ?>" class="btn btn-secondary">Back to Create Order</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><h5>List of Available Tailors</h5></div>
        <div class="card-body">
            <table id="tailorTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Garment Types</th>
                        <th>Materials</th>
                        <th>Region</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tailors as $t): ?>
                    <tr>
                        <td>
                            <a href="<?= base_url('orders/tailor_details/' . $t->id) ?>" class="fw-semibold text-decoration-none">
                                <?= esc($t->options['organization'] ?? $t->username) ?>
                            </a>
                        </td>
                        <!-- <td><strong><?= $t->options['organization'] ?? $t->username ?></strong></td> -->
                        <td><small><?= $t->options['tailor_type'] ?? 'General' ?></small></td>
                        <td><small><?= $t->options['tailor_material'] ?? 'Any' ?></small></td>
                        <td><?= $t->region ?></td>
                        <td><?= $t->options['mobile'] ?? 'N/A' ?></td>
                        <td>
                            <a href="<?= base_url('orders/create/'.$t->id) ?>" class="btn btn-sm btn-primary">Ask for Appointment</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tailorTable').DataTable({
            searching: true,
            language: {
                search: "Keyword:",
                searchPlaceholder: "Type keyword..."
            }
        });
    });
</script>
<?= $this->endSection() ?>
