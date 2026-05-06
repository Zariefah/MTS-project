<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<?= view('partials/alert') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Create New Order</h3>
        <div>
            <?php if (!$tailor): ?>
                <a href="<?= base_url('orders/search_tailor') ?>" class="btn btn-secondary">Create order with a Tailor</a>
                <a href="<?= base_url('orders') ?>" class="btn btn-secondary">Back to Orders</a>

            <?php endif; ?>
            <?php if ($tailor): ?>
                <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <?php if($tailor): ?>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Company Details</div>
                <div class="card-body small">
                    <h6><?= $options['organization'] ?? $tailor->username ?></h6>
                    <p>SSM: <?= $tailor->ssm_registration ?? 'N/A' ?><br>
                    Region: <?= $tailor->region ?><br>
                    Address: <?= $tailor->address ?></p>
                    <strong>Specialties:</strong> <?= $options['tailor_type'] ?? 'N/A' ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="<?= $tailor ? 'col-md-8' : 'col-md-12' ?>">
            <form action="<?= base_url('orders/store') ?>" method="post" class="card shadow-sm p-4" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="preferred_tailor_id" value="<?= $preferred_id ?>">
                <h5>New Order Details</h5>
                <div class="mb-3">
                    <label>Garment Type</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Select garment type --</option>
                        <?php foreach ($garment_options as $gt): ?>
                            <option value="<?= esc($gt) ?>"><?= esc($gt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Material</label>
                    <select name="material" class="form-select" required id="materialSelect">
                        <option value="">-- Select material --</option>
                        <?php foreach ($material_options as $mat): ?>
                            <option value="<?= esc($mat) ?>"><?= esc($mat) ?></option>
                        <?php endforeach; ?>
                        <option value="__other__">Other</option>
                    </select>
                    <input
                        type="text"
                        name="material_other"
                        id="materialOtherInput"
                        class="form-control mt-2 d-none"
                        placeholder="Type your material (e.g. Polyester blend, Denim, etc.)"
                    >
                </div>
                <div class="mb-3">
                    <label>Expected Completion</label>
                    <input type="date" name="expected_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Desired Design Photos</label>
                    <p class="text-muted small mb-2">Upload clear photos of the design you want (so tailors can confirm capability before bidding).</p>
                    <input type="file" name="design_images[]" class="form-control" accept="image/*" multiple required>
                    <div id="designPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Material Photos</label>
                    <p class="text-muted small mb-2">Upload the material/cloth photos (so tailors can confirm the fabric is correct).</p>
                    <input type="file" name="material_images[]" class="form-control" accept="image/*" multiple required>
                    <div id="materialPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Offered Appointment Dates (select multiple)</label>
                    <p class="text-muted small mb-2">Click dates on the calendar to add or remove them <strong>(Pick 3 Dates Maximum)</strong>.</p>
                    <div id="offered-appointment-calendar" class="d-none" section="centre" ></div>
                    <div id="offered_dates_inputs" class="d-none" aria-hidden="true"></div>
                    <div id="offered-dates-summary" class="small text-secondary mt-2"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Order</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
(function () {
    var calEl = document.getElementById('offered-appointment-calendar');
    var hiddenWrap = document.getElementById('offered_dates_inputs');
    var summaryEl = document.getElementById('offered-dates-summary');
    var form = calEl && calEl.closest('form');
    if (!calEl || !form) {
        return;
    }

    function syncHiddenInputs(selectedDates) {
        hiddenWrap.innerHTML = '';
        selectedDates.forEach(function (d) {
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'offered_dates[]';
            inp.value = y + '-' + m + '-' + day;
            hiddenWrap.appendChild(inp);
        });
        if (selectedDates.length === 0) {
            summaryEl.textContent = 'No dates selected yet.';
        } else {
            var labels = selectedDates.map(function (d) {
                return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
            });
            summaryEl.textContent = 'Selected: ' + labels.join(', ');
        }
    }

    var fp = flatpickr(calEl, {
        inline: true,
        mode: 'multiple',
        minDate: 'today',
        dateFormat: 'Y-m-d',
        onChange: function (selectedDates) {
            syncHiddenInputs(selectedDates);
        }
    });

    syncHiddenInputs(fp.selectedDates);

    form.addEventListener('submit', function (e) {
        if (fp.selectedDates.length < 1) {
            e.preventDefault();
            alert('Please select at least one offered appointment date on the calendar.');
        }
    });
})();
</script>

<script>
  (function () {
    const materialSelect = document.getElementById('materialSelect');
    const materialOtherInput = document.getElementById('materialOtherInput');

    function syncMaterialOther() {
      const isOther = materialSelect && materialSelect.value === '__other__';
      if (isOther) {
        materialOtherInput.classList.remove('d-none');
        materialOtherInput.required = true;
      } else {
        materialOtherInput.classList.add('d-none');
        materialOtherInput.required = false;
        materialOtherInput.value = '';
      }
    }

    if (materialSelect) {
      materialSelect.addEventListener('change', syncMaterialOther);
      syncMaterialOther();
    }

    // Quick preview helpers
    function bindPreview(inputId, previewId) {
      const inputEl = document.getElementById(inputId);
      const previewEl = document.getElementById(previewId);
      if (!inputEl || !previewEl) return;

      inputEl.addEventListener('change', function () {
        previewEl.innerHTML = '';
        const files = inputEl.files ? Array.from(inputEl.files) : [];
        files.slice(0, 6).forEach((f) => {
          const reader = new FileReader();
          reader.onload = function (ev) {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.style.width = '64px';
            img.style.height = '64px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px';
            img.className = 'border bg-white';
            previewEl.appendChild(img);
          };
          reader.readAsDataURL(f);
        });
      });
    }

    // Input elements are anonymous in HTML; use query by name
    const designInput = document.querySelector('input[name="design_images[]"]');
    const materialInput = document.querySelector('input[name="material_images[]"]');

    if (designInput) {
      designInput.addEventListener('change', function () {
        const previewEl = document.getElementById('designPreview');
        if (!previewEl) return;
        previewEl.innerHTML = '';
        const files = designInput.files ? Array.from(designInput.files) : [];
        files.slice(0, 6).forEach((f) => {
          const reader = new FileReader();
          reader.onload = function (ev) {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.style.width = '64px';
            img.style.height = '64px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px';
            img.className = 'border bg-white';
            previewEl.appendChild(img);
          };
          reader.readAsDataURL(f);
        });
      });
    }

    if (materialInput) {
      materialInput.addEventListener('change', function () {
        const previewEl = document.getElementById('materialPreview');
        if (!previewEl) return;
        previewEl.innerHTML = '';
        const files = materialInput.files ? Array.from(materialInput.files) : [];
        files.slice(0, 6).forEach((f) => {
          const reader = new FileReader();
          reader.onload = function (ev) {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.style.width = '64px';
            img.style.height = '64px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px';
            img.className = 'border bg-white';
            previewEl.appendChild(img);
          };
          reader.readAsDataURL(f);
        });
      });
    }
  })();
</script>
<?= $this->endSection() ?>
