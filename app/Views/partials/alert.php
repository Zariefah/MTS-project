<!-- Profile completion (all logged-in users; blue, not dismissible until complete) -->
<?php if (logged_in() && !user_profile_is_complete()) :
  $profileUrl = base_url('users/profile/' . user_id());
  echo $bs->alert($data = [
    'type' => 'primary',
    'icon' => '',
    'title' => lang('Auth.alert.profile_incomplete_title'),
    'subject' => '',
    'text' => '<p class="mb-2">' . lang('Auth.alert.profile_incomplete_text') . '</p>'
      . '<p class="mb-0"><a class="alert-link fw-semibold" href="' . esc($profileUrl, 'attr') . '">'
      . lang('Auth.alert.profile_incomplete_link') . '</a></p>',
    'help' => '',
    'dismissible' => false,
  ]);
endif ?>

<!-- Measurement completion (customers only; blue, not dismissible until complete) -->
<?php if (logged_in() && in_roles('Customer') && !customer_measurements_are_complete()) :
  $measUrl = base_url('measurements');
  echo $bs->alert($data = [
    'type' => 'primary',
    'icon' => '',
    'title' => lang('Auth.alert.measurement_incomplete_title'),
    'subject' => '',
    'text' => '<p class="mb-2">' . lang('Auth.alert.measurement_incomplete_text') . '</p>'
      . '<p class="mb-0"><a class="alert-link fw-semibold" href="' . esc($measUrl, 'attr') . '">'
      . lang('Auth.alert.measurement_incomplete_link') . '</a></p>',
    'help' => '',
    'dismissible' => false,
  ]);
endif ?>

<!-- Information Alert -->
<?php if (session()->has('message')) :
  echo $bs->alert($data = [
    'type' => 'info',
    'icon' => '',
    'title' => lang('Auth.alert.information'),
    'subject' => session('message'),
    'text' => '',
    'help' => '',
    'dismissible' => true,
  ]);
endif ?>

<!-- Success Alert -->
<?php if (session()->has('success')) :
  echo $bs->alert($data = [
    'type' => 'success',
    'icon' => '',
    'title' => lang('Auth.alert.success'),
    'subject' => session('success'),
    'text' => '',
    'help' => '',
    'dismissible' => true,
  ]);
endif ?>

<!-- Warning Alert -->
<?php if (session()->has('warning')) :
  echo $bs->alert($data = [
    'type' => 'warning',
    'icon' => '',
    'title' => lang('Auth.alert.warning'),
    'subject' => session('warning'),
    'text' => '',
    'help' => '',
    'dismissible' => true,
  ]);
endif ?>

<!-- Single Error Alert -->
<?php if (session()->has('error')) :
  echo $bs->alert($data = [
    'type' => 'danger',
    'icon' => '',
    'title' => lang('Auth.alert.error'),
    'subject' => session('error'),
    'text' => '',
    'help' => '',
    'dismissible' => true,
  ]);
endif ?>

<!-- Multiple Errors Alert -->
<?php if (session()->has('errors')) :
  $text = '<ul>';
  if (is_array(session('errors'))) {
    foreach (session('errors') as $error) {
      $text .= '<li>' . $error . '</li>';
    }
  } else {
    $text .= '<li>' . session('errors') . '</li>';
  }
  $text .= '</ul>';
  echo $bs->alert($data = [
    'type' => 'danger',
    'icon' => '',
    'title' => lang('Auth.alert.error'),
    'subject' => '',
    'text' => $text,
    'help' => '',
    'dismissible' => true,
  ]);
endif ?>
