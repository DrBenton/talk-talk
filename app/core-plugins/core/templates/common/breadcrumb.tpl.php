<?php
if ($this->app()->vars['isAjax']) {
    // We use a temporary container when in an Ajax context
    $tmpBreadcrumbContainerId = $this->app()->get('uuid')->numeric();
    $breadcrumbContainerId = 'breadcrumb-' . $tmpBreadcrumbContainerId;
} else {
    // We use a real, permanent container when not in an Ajax context
    $breadcrumbContainerId = 'breadcrumb';
}
?>

<?= $this->hooks()->html('breadcrumb') ?>
<ul id="<?= $breadcrumbContainerId ?>"
    class="breadcrumb <?= ($this->app()->vars['isAjax']) ? 'hidden' : '' ?>">
    <?php if (!empty($this->data)): ?>
        <?php foreach($this->data as $index => $breadcrumbPart): ?>
        <li class="breadcrumb-item level-<?= $index ?> <?= isset($breadcrumbPart['class']) ? $this->e($breadcrumbPart['class']) : '' ?>">
            <a href="<?= $this->e($breadcrumbPart['url']) ?>"
               class="breadcrumb-link ajax-link">
                <?= $this->transE($breadcrumbPart['label'], isset($breadcrumbPart['labelParams']) ? $breadcrumbPart['labelParams'] : array()) ?>
            </a>
        </li>
        <?php endforeach ?>
    <?php endif ?>
</ul>

<?php if ($this->app()->vars['isAjax']): ?>
    <script>
        require(["jquery"], function ($) {
            // This breadcrumb is displayed in the layout #breadcrumb
            var breadcrumbToDisplaySelector = "#<?= $breadcrumbContainerId ?>";
            $(document).trigger("uiNeedsBreadcrumbUpdate", {
                fromSelector: breadcrumbToDisplaySelector
            });
        });
    </script>
<?php endif ?>
