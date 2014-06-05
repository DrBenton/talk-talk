<?php
$visibilityClass = (
    (!empty($this->options['onlyForAuthenticated']) && !$this->app()->vars['isAuthenticated']) ||
    (!empty($this->options['onlyForAnonymous']) && !$this->app()->vars['isAnonymous'])
) ? 'hidden' : '' ;
?>
<li class="<?= $this->options['class'] ?> <?= $visibilityClass ?>">
    <a href="<?= $this->url ?>" class="<?= $this->options['ajaxLink'] ? 'ajax-link' : '' ?>"><?= $this->trans($this->label) ?></a>
</li>