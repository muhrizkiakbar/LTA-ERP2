<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/waves.js') }}"></script>
<script src="{{ asset('assets/js/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/theme.js') }}"></script>
<?php if (!empty($assets['script'])): ?>
  <?php foreach ($assets['script'] as $script): ?>
    <script src="{{ asset($script) }}"></script>
  <?php endforeach ?>
<?php endif ?>