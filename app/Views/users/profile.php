<?= $this->extend(config('App')->viewLayout) ?>
<?= $this->section('main') ?>
<div class="container">

  <?= view('partials/alert') ?>

  <form id="profile-edit-form" action="<?= base_url() ?>/users/profile/<?= $user->id ?>" method="post">
    <?= csrf_field() ?>

    <div class="card">

      <?= $bs->cardHeader([ 'icon' => 'bi-person-square', 'title' => lang('Auth.btn.editProfile') . ' ' . lang('General.for') . ' ' . $user->username, 'help' => getPageHelpUrl(uri_string()) ]) ?>

      <div class="card-body">

        <div class="card">

          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button aria-controls="personal-tab-pane" aria-selected="true" class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal-tab-pane" id="personal-tab" role="tab" type="button">
                  <?= lang('Profile.tab.personal') ?>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button aria-controls="contact-tab-pane" aria-selected="false" class="nav-link" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" id="contact-tab" role="tab" type="button">
                  <?= lang('Profile.tab.contact') ?>
                </button>
              </li>
              
              <?php if (in_roles('Tailor')): ?>
              <li class="nav-item" role="presentation">
                <button aria-controls="specialties-tab-pane" aria-selected="false" class="nav-link" data-bs-toggle="tab" data-bs-target="#specialties-tab-pane" id="specialties-tab" role="tab" type="button">
                  Specialties
                </button>
              </li>
              <?php endif; ?>

              <li class="nav-item" role="presentation">
                <button aria-controls="options-tab-pane" aria-selected="false" class="nav-link" data-bs-toggle="tab" data-bs-target="#options-tab-pane" id="options-tab" role="tab" type="button">
                  <?= lang('Profile.tab.options') ?>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button aria-controls="avatar-tab-pane" aria-selected="false" class="nav-link" data-bs-toggle="tab" data-bs-target="#avatar-tab-pane" id="avatar-tab" role="tab" type="button">
                  <?= lang('Profile.tab.avatar') ?>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button aria-controls="2fa-tab-pane" aria-selected="false" class="nav-link" data-bs-toggle="tab" data-bs-target="#2fa-tab-pane" id="2fa-tab" role="tab" type="button">
                  <?= lang('Profile.tab.2fa') ?>
                </button>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <div class="tab-content" id="myTabContent">

              <div class="tab-pane fade show active" id="personal-tab-pane" role="tabpanel">
                <div class="card-body">
                    <?php
                    echo $bs->formRow([
                        'type' => 'text', 'name' => 'email', 'title' => 'Email',
                        'mandatory' => true,
                        'value' => $user->email
                    ]);
                    echo $bs->formRow([
                        'type' => 'text', 'name' => 'username', 'title' => 'Username',
                        'mandatory' => true,
                        'value' => $user->username
                    ]);

                    if (in_roles('Customer')): ?>
                        <?= $bs->formRow(['type' => 'text', 'name' => 'firstname', 'title' => 'First Name', 'mandatory' => true,'value' => $user->firstname]) ?>
                        <?= $bs->formRow(['type' => 'text', 'name' => 'lastname', 'title' => 'Last Name', 'mandatory' => true,'value' => $user->lastname]) ?>
                    <?php endif; ?>

                    <?php if (in_roles('Tailor')): ?>
                        <?= $bs->formRow(['type' => 'text', 'name' => 'organization', 'title' => 'Shop Name', 'mandatory' => true, 'value' => $profile['organization'] ?? '']) ?>
                    <?php endif; ?>

                    <?= $bs->formRow([
                        'type'      => 'textarea',
                        'name'      => 'address',
                        'title'     => 'Address',
                        'desc'      => 'Enter your full residential or business address',
                        'mandatory' => true,
                        'value'     => $user->address
                    ]) ?>

                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label">Region (Pulau Pinang)</label>
                        <div class="col-md-9">
                            <select name="region" class="form-select">
                                <option value="" disabled <?= empty($user->region) ? 'selected' : '' ?>>-- Select Region --</option>
                                <?php foreach ($regions as $area => $towns): ?>
                                    <optgroup label="<?= $area ?>">
                                        <?php foreach ($towns as $town): ?>
                                            <option value="<?= $town ?>" <?= ($user->region == $town) ? 'selected' : '' ?>><?= $town ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">Select the district in Penang where you are located.</div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel">
                <div class="card-body">
                    <?php if (in_roles('Tailor')): ?>
                        <?= $bs->formRow(['type' => 'text', 'name' => 'phone', 'title' => 'Office Phone', 'mandatory' => true, 'value' => $profile['phone'] ?? '']) ?>
                    <?php endif; ?>

                    <?= $bs->formRow(['type' => 'text', 'name' => 'mobile', 'title' => 'Mobile Number','mandatory' => true,'value' => $profile['mobile'] ?? '']) ?>

                    <?php if (in_roles('Tailor')): ?>
                        <?= $bs->formRow(['type' => 'text', 'name' => 'facebook', 'title' => 'Facebook URL', 'value' => $profile['facebook'] ?? '']) ?>
                        <?= $bs->formRow(['type' => 'text', 'name' => 'instagram', 'title' => 'Instagram URL', 'value' => $profile['instagram'] ?? '']) ?>
                    <?php endif; ?>

                </div>
              </div>
              <?php if (in_roles('Tailor')): ?>
              <div class="tab-pane fade" id="specialties-tab-pane" role="tabpanel" aria-labelledby="specialties-tab">
                  <div class="card">
                      <div class="card-body">
                          
                          <div class="mb-4">
                              <label class="form-label fw-bold d-block mb-3">Jenis Pakaian (Garment Specialties)<span class="text-danger">*</span></label>
                              <?php 
                              $savedTypes = isset($profile['tailor_type']) ? explode(',', $profile['tailor_type']) : [];
                              $types = ['Baju Kurung', 'Baju Melayu', 'Kemeja', 'Seluar', 'Skirt', 'Blouse', 'Jubah', 'Kebaya'];
                              ?>
                              <div>
                                  <?php foreach ($types as $t): ?>
                                      <div class="form-check form-check-inline mb-2" style="min-width: 150px;">
                                          <input class="form-check-input" type="checkbox" name="tailor_type[]" value="<?= $t ?>" id="type_<?= $t ?>" <?= in_array($t, $savedTypes) ? 'checked' : '' ?> required>
                                          <label class="form-check-label" for="type_<?= $t ?>"><?= $t ?></label>
                                      </div>
                                  <?php endforeach; ?>
                              </div>
                              <div class="form-text text-danger" id="tailor_type_error" style="display:none">Please select at least one garment specialty.</div>
                          </div>
                          <script>
                          // Make the garment specialties group mandatory (at least one required)
                          document.addEventListener('DOMContentLoaded', function () {
                              var checkboxes = document.querySelectorAll('input[name="tailor_type[]"]');
                              var errorMsg = document.getElementById('tailor_type_error');
                              if (checkboxes.length > 0) {
                                  function validateTailorType() {
                                      var checked = Array.prototype.slice.call(checkboxes).some(ch => ch.checked);
                                      checkboxes.forEach(cb => cb.required = !checked);
                                      if (!checked) {
                                          errorMsg.style.display = '';
                                      } else {
                                          errorMsg.style.display = 'none';
                                      }
                                  }
                                  checkboxes.forEach(cb => {
                                      cb.addEventListener('change', validateTailorType);
                                  });
                                  // Initial validation if editing profile and none are selected
                                  validateTailorType();
                              }
                          });
                          </script>

                          <hr>

                          <div class="mb-4">
                              <label class="form-label fw-bold d-block mb-3">Jenis Material (Fabric Specialties)<span class="text-danger">*</span></label>
                              <?php 
                              $savedMats = isset($profile['tailor_material']) ? explode(',', $profile['tailor_material']) : [];
                              $materials = ['Cotton', 'Silk', 'Lycra', 'Chiffon', 'Satin', 'Batik', 'Linen', 'Songket'];
                              ?>
                              <div>
                                  <?php foreach ($materials as $m): ?>
                                      <div class="form-check form-check-inline mb-2" style="min-width: 150px;">
                                          <input class="form-check-input" type="checkbox" name="tailor_material[]" value="<?= $m ?>" id="mat_<?= $m ?>" <?= in_array($m, $savedMats) ? 'checked' : '' ?> required>
                                          <label class="form-check-label" for="mat_<?= $m ?>"><?= $m ?></label>
                                      </div>
                                  <?php endforeach; ?>
                              </div>
                              <div class="form-text text-danger" id="tailor_material_error" style="display:none">Please select at least one fabric specialty.</div>
                          </div>
                          <script>
                          // Make the fabric specialties group mandatory (at least one required)
                          document.addEventListener('DOMContentLoaded', function () {
                              var matCheckboxes = document.querySelectorAll('input[name="tailor_material[]"]');
                              var matErrorMsg = document.getElementById('tailor_material_error');
                              if (matCheckboxes.length > 0) {
                                  function validateTailorMaterial() {
                                      var checked = Array.prototype.slice.call(matCheckboxes).some(ch => ch.checked);
                                      matCheckboxes.forEach(cb => cb.required = !checked);
                                      if (!checked) {
                                          matErrorMsg.style.display = '';
                                      } else {
                                          matErrorMsg.style.display = 'none';
                                      }
                                  }
                                  matCheckboxes.forEach(cb => {
                                      cb.addEventListener('change', validateTailorMaterial);
                                  });
                                  // Initial validation if editing profile and none are selected
                                  validateTailorMaterial();
                              }
                          });
                          </script>

                      </div>
                  </div>
              </div>
              <?php endif; ?>
              <div class="tab-pane fade" id="options-tab-pane" role="tabpanel">
                <div class="card-body">
                  <?php
                  echo $bs->formRow([
                    'type' => 'radio',
                    'mandatory' => false,
                    'name' => 'theme',
                    'title' => lang('Profile.theme'),
                    'desc' => lang('Profile.theme_desc'),
                    'items' => array(
                      [ 'label' => lang('General.default'), 'value' => 'default', 'checked' => ($profile['theme'] === 'default' ? true : false) ],
                      [ 'label' => lang('Profile.light'), 'value' => 'light', 'checked' => ($profile['theme'] === 'light' ? true : false) ],
                      [ 'label' => lang('Profile.dark'), 'value' => 'dark', 'checked' => ($profile['theme'] === 'dark' ? true : false) ]
                    )
                  ]);
                  echo $bs->formRow([
                    'type' => 'radio',
                    'mandatory' => false,
                    'name' => 'menu',
                    'title' => lang('Profile.menu'),
                    'desc' => lang('Profile.menu_desc'),
                    'items' => array(
                      [ 'label' => lang('General.default'), 'value' => 'default', 'checked' => ($profile['menu'] === 'default' ? true : false) ],
                      [ 'label' => lang('Profile.navbar'), 'value' => 'navbar', 'checked' => ($profile['menu'] === 'navbar' ? true : false) ],
                      [ 'label' => lang('Profile.sidebar'), 'value' => 'sidebar', 'checked' => ($profile['menu'] === 'sidebar' ? true : false) ]
                    )
                  ]);
                  echo $bs->formRow([
                    'type' => 'select',
                    'subtype' => 'single',
                    'name' => 'language',
                    'mandatory' => false,
                    'title' => lang('Profile.language'),
                    'desc' => lang('Profile.language_desc'),
                    'items' => $languageOptions,
                  ]);
                  ?>
                </div>
              </div>
              <div class="tab-pane fade" id="avatar-tab-pane" role="tabpanel">
                <div class="card-body">
                  <div class="row">
                    <div class="col"><strong><?= lang('Profile.avatar_current') ?></strong></div>
                    <div class="col">
                      <?php
                      $avatar = ($profile['avatar'] == "gravatar") ? $gravatarUrl : $avaUrl . $profileAvatar;
                      ?>
                      <img src="<?= $avatar ?>" width="72" height="72" alt="">
                    </div>
                  </div>
                  <hr class="my-4">
                  <div class="row">
                    <label class="col" for="gravatar"><strong><?= lang('Profile.use_gravatar') ?></strong></label>
                    <div class="col">
                      <img src="<?= $gravatarUrl ?>" width="72" height="72" alt="" class="float-start me-2">
                      <input id="gravatar" name="avatar" type="radio" value="gravatar" <?= $profile['avatar'] === 'gravatar' ? 'checked' : '' ?>>
                    </div>
                  </div>
                  <hr class="my-4">
                  <?php foreach (config('App')->avatarSets as $set): ?>
                    <div class="row">
                      <div class="col">
                        <strong>"<?= $set['title'] ?>" Avatars</strong><br>
                        <span class="text-normal small"><?= lang('Profile.avatar_credits') ?> <a href="<?= $set['creditsLink'] ?>" target="_blank"><?= $set['creditsName'] ?></a>.</span>
                      </div>
                      <div class="col">
                        <img src="<?= $avaUrl . $set['sample'] ?>" style="width:72px;height:72px;" alt="">
                        <button class="btn btn-primary mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $set['set'] ?>"><?= lang('Profile.avatar_show') ?>...</button>
                      </div>
                      <div class="collapse col-12 mt-4" id="collapse-<?= $set['set'] ?>">
                        <?php foreach ($avatars as $avatar): 
                          $pieces = explode('_', $avatar);
                          if (isset($pieces[1]) && $pieces[1] === $set['set']): 
                            $checked = ($profileAvatar == $avatar) ? 'checked="checked"' : '';
                            $border = ($profileAvatar == $avatar) ? 'border: 2px solid #dd0000; padding: 2px;' : 'border: 1px solid #eeeeee; padding: 4px;';
                          ?>
                            <div class="float-start me-2 mb-2" style="<?= $border ?>">
                              <label><input name="opt_avatar" type="radio" value="<?= $avatar ?>" <?= $checked ?>></label>
                              <img src="<?= $avaUrl . $avatar ?>" style="width:72px;height:72px;" alt="">
                            </div>
                          <?php endif; endforeach; ?>
                      </div>
                    </div>
                    <hr class="my-4">
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="tab-pane fade" id="2fa-tab-pane" role="tabpanel">
                <div class="card-body">
                  <?php if (user()->hasSecret()): ?>
                    <div class="alert alert-warning">
                      <?= lang('Auth.2fa.setup.secret_exists') ?>
                      <div>
                        <a href="<?= base_url() . '/setup2fa' ?>" class="btn btn-secondary mt-2"><?= lang('Navbar.user.setup2fa') ?></a>
                        <?php if (!$settings['require2fa']): ?>
                          <button type="button" class="btn btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#modalRemoveSecret"><i class="bi bi-trash"></i> <?= lang('Auth.btn.remove_secret') ?></button>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-info">
                      <?= lang('Profile.2fa_desc') ?>
                      <div><a href="<?= base_url() . '/setup2fa' ?>" class="btn btn-secondary mt-2"><?= lang('Navbar.user.setup2fa') ?></a></div>
                    </div>
                  <?php endif; ?>
                  
                  <?= $bs->modal([
                    'id' => 'modalRemoveSecret',
                    'header' => lang('Auth.modal.confirm'),
                    'header_color' => 'danger',
                    'body' => lang('Profile.remove_secret_confirm') . "<br><br>" . lang('Profile.remove_secret_confirm_desc'),
                    'btn_color' => 'danger',
                    'btn_name' => 'btn_remove_secret',
                    'btn_text' => lang('Auth.btn.remove_secret'),
                  ]); ?>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <button type="submit" class="btn btn-primary"><?= lang('Auth.btn.submit') ?></button>
        <a class="btn btn-secondary float-end" href="<?= base_url() ?>"><?= lang('Auth.btn.cancel') ?></a>
      </div>
    </div>
  </form>
  <script>
  (function () {
    var form = document.getElementById('profile-edit-form');
    if (!form) {
      return;
    }
    var msg = <?= json_encode(lang('Profile.mandatory_fields_alert')) ?>;
    var isTailor = <?= in_roles('Tailor') ? 'true' : 'false' ?>;
    var isCustomer = <?= in_roles('Customer') ? 'true' : 'false' ?>;

    function showTabFor(el) {
      if (!el) {
        return;
      }
      var pane = el.closest('.tab-pane');
      if (!pane || !pane.id) {
        return;
      }
      var btn = document.querySelector('[data-bs-target="#' + pane.id + '"]');
      if (btn && window.bootstrap && bootstrap.Tab) {
        bootstrap.Tab.getOrCreateInstance(btn).show();
      }
    }

    form.addEventListener('submit', function (e) {
      var trim = function (s) {
        return (s || '').trim();
      };

      var el;

      el = form.querySelector('[name="email"]');
      if (!trim(el && el.value)) {
        e.preventDefault();
        alert(msg);
        showTabFor(el);
        if (el) {
          el.focus();
        }
        return;
      }

      el = form.querySelector('[name="username"]');
      if (!trim(el && el.value)) {
        e.preventDefault();
        alert(msg);
        showTabFor(el);
        if (el) {
          el.focus();
        }
        return;
      }

      if (isCustomer) {
        el = form.querySelector('[name="firstname"]');
        if (!trim(el && el.value)) {
          e.preventDefault();
          alert(msg);
          showTabFor(el);
          if (el) {
            el.focus();
          }
          return;
        }
        el = form.querySelector('[name="lastname"]');
        if (!trim(el && el.value)) {
          e.preventDefault();
          alert(msg);
          showTabFor(el);
          if (el) {
            el.focus();
          }
          return;
        }
      }

      if (isTailor) {
        el = form.querySelector('[name="organization"]');
        if (!trim(el && el.value)) {
          e.preventDefault();
          alert(msg);
          showTabFor(el);
          if (el) {
            el.focus();
          }
          return;
        }
      }

      el = form.querySelector('[name="address"]');
      if (!trim(el && el.value) || trim(el && el.value).length < 10) {
        e.preventDefault();
        alert(msg);
        showTabFor(el);
        if (el) {
          el.focus();
        }
        return;
      }

      el = form.querySelector('[name="region"]');
      if (!el || !trim(el.value)) {
        e.preventDefault();
        alert(msg);
        showTabFor(el);
        if (el) {
          el.focus();
        }
        return;
      }

      if (isTailor) {
        el = form.querySelector('[name="phone"]');
        if (!trim(el && el.value)) {
          e.preventDefault();
          alert(msg);
          showTabFor(el);
          if (el) {
            el.focus();
          }
          return;
        }
      }

      el = form.querySelector('[name="mobile"]');
      if (!trim(el && el.value)) {
        e.preventDefault();
        alert(msg);
        showTabFor(el);
        if (el) {
          el.focus();
        }
        return;
      }

      if (isTailor) {
        var typeChecked = form.querySelectorAll('input[name="tailor_type[]"]:checked');
        if (typeChecked.length === 0) {
          e.preventDefault();
          alert(msg);
          el = form.querySelector('input[name="tailor_type[]"]');
          showTabFor(el);
          if (el) {
            el.focus();
          }
          return;
        }
        var matChecked = form.querySelectorAll('input[name="tailor_material[]"]:checked');
        if (matChecked.length === 0) {
          e.preventDefault();
          alert(msg);
          el = form.querySelector('input[name="tailor_material[]"]');
          showTabFor(el);
          if (el) {
            el.focus();
          }
          return;
        }
      }
    });
  })();
  </script>
</div>
<?= $this->endSection() ?>