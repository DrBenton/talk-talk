<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><!-- TODO: {{ app['forum-base.title'] }} --></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- TODO:
    <meta name="csrf-param" content="{{ app['csrf.token_name'] }}"/>
    <meta name="csrf-token" content="{{ app['csrf.token_value'] }}"/>
    -->

    <!-- TODO:
    {{ enable_html_hooks('app_stylesheets') }}
    {% for stylesheet in get_plugins_stylesheets() %}
    <link rel="stylesheet" href="{{ stylesheet }}">
    {% endfor %}

    {# Do we have "HEAD" JavaScript files? Let's include them now! #}
    {% include 'core/common/javascripts-inclusion.twig'
    with { javascripts: get_plugins_javascripts('head') } %}
    -->

</head>
<body>
<!--[if lt IE 8]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<!-- TODO: {{ enable_html_hooks('site_container') }} -->
<div id="site-container">

    <?php if (isset($this->header)): /*a custom header has been requested*/ ?>
        <?= $this->header ?>
    <?php else: /*default header*/ ?>
        <?= $this->insert('core::common/header') ?>
    <?php endif ?>

    <?= $this->insert('core::common/alerts-display') ?>

    <?php if (isset($this->breadcrumb)): /*a custom breadcrumb has been requested*/ ?>
        <?= $this->breadcrumb ?>
    <?php else: /*default breadcrumb*/ ?>
        <?= $this->insert('core::common/breadcrumb') ?>
    <?php endif ?>

    <!-- TODO: {{ enable_html_hooks('main_content_container') }} -->
    <div id="main-content-container">
        <div id="main-content" class="clearfix">
            <?= $this->content() ?>
        </div>
    </div>

    <?php if (isset($this->footer)): /*a custom footer has been requested*/ ?>
        <?= $this->footer ?>
    <?php else: /*default footer*/ ?>
        <?= $this->insert('core::common/footer') ?>
    <?php endif ?>

    <!-- TODO: {% include 'core/common/js-data.twig' %} -->

    <!-- TODO: {% include 'utils/debug/app-perfs-info.twig' %} -->

</div><?php /* end #site-container */ ?>

<!-- TODO:
{# JavaScript, it's up to you now! #}
{% include 'core/common/javascripts-inclusion.twig'
with { javascripts: get_plugins_javascripts('endOfBody') } %}
-->
</body>
</html>