<?php $this->layout('core::layouts/main-layout') ?>

<?php
// Some "JS build" layout specific stuff
$this->endOfBodyJsOpts = array('jsFilesBuild' => true);
?>

<style>
    textarea {
        width: 100%;
        min-height: 10em;
    }
</style>

<div class="app-js-core-compilation-gui-container flight-component"
    data-component="app/utils/components/data/app-js-core-compilation-handler">

    <h1>r.js build in the browser</h1>

    <p>
        <button id="build" class="hidden">Build it</button>
    </p>

    <h2>Build Messages</h2>
    <textarea id="buildMessages"></textarea>

    <h2>Output</h2>
    <textarea id="output"></textarea>

    <p>
        <button id="save" class="hidden">Save</button>
    </p>


    <?php
    /* JavaScripts to compile */
    $jsFilesToCompile = $this->appAssets()->getJsModulesToCompile();
    ?>
    <div id="app-js-core-files-to-compile"
         data-files="<?= $this->e(json_encode($jsFilesToCompile)) ?>"></div>

</div>

