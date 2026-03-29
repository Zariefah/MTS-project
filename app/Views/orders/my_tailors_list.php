<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>My Tailors List</h3>
    </div>

    <table id="myTailorsListTable" class="table table-bordered bg-white shadow-sm">
        <thead class="bg-light">
            <tr>
                <th>Tailor Name</th>
                <th>Order ID</th>
                <th>Completed Date</th>
                <th>Pickup Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= $o['tailor_name'] ?? '-' ?></td>
                <td>#<?= $o['id'] ?></td>
                <td><?= $o['completion_date'] ?? '-' ?></td>
                <td><?= !empty($o['pickup_date']) ? date('d M Y', strtotime($o['pickup_date'])) : '-' ?></td>
                <td>
                    <span class="badge bg-<?= ($o['status'] == 'Completed' ? 'success' : 'primary') ?>">
                        <?= $o['status'] ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; if (empty($orders)) echo "<tr><td colspan='3' class='text-center'>No tailor records found.</td></tr>"; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#myTailorsListTable').DataTable({
            searching: true,
            language: {
                search: "Keyword:",
                searchPlaceholder: "Type keyword..."
            }
        });
    });
</script>
<?= $this->endSection() ?>
