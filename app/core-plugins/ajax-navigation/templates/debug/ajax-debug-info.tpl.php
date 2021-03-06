<?= $this->hooks()->html('ajax_loadings_debug_info') ?>
<fieldset id="ajax-loadings-debug-info" class="hidden">
    <legend>Ajax page contents loadings info</legend>
    <ul>
        <li>
            Current Action URL: <code class="current-action-url"></code>
            <span class="current-action-has-been-loaded-from-cache hidden">(loaded from cache)</span>
        </li>
        <li>
            Last Action loading duration: <b class="current-action-loading-duration"></b>ms.
        </li>
        <li>
            Actions loaded from Ajax: <b class="nb-actions-loaded"></b>
        </li>
        <li>
            Average Actions real loading duration: <b class="average-actions-loading-duration"></b>ms.
        </li>
        <li>
            Actions loaded instantly from cache: <b class="nb-actions-loaded-from-cache">0</b>
        </li>
        <li>
            Average Actions loading duration with actions loaded from cache: <b class="average-actions-loading-duration-with-cache"></b>ms.
        </li>
    </ul>
</fieldset>
