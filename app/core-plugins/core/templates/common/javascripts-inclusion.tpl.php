<?php foreach($this->javascripts as $jsResource): ?>
<?php if (isset($jsResource['ieCondition'])) { ?><!--[if <?= $jsResource['ieCondition'] ?>]><?php } ?>
<script src="<?= $jsResource['url'] ?>"></script>
<?php if (isset($jsResource['ieCondition'])) { ?><![endif]--><?php } ?>
<?php endforeach ?>