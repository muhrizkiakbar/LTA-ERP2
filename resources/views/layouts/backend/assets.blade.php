<?php if (!empty($assets['style'])): ?>
  <?php foreach ($assets['style'] as $style): ?>
    <link href="{{ asset($style) }}" rel="stylesheet">
  <?php endforeach ?>
<?php endif ?>
<link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" type="text/css" />