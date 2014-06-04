<!-- TODO:
{% if app.isAjax %}
{# We use a temporary container when in an Ajax context #}
{% set tmpNotificationsContainerId = app.uuid %}
{% set breadcrumbContainerId = "breacrumb-" ~ tmpNotificationsContainerId %}
{% else { %}# We use a real, permanent container when not in an Ajax context #}
{% set breadcrumbContainerId = "breadcrumb" %}
{% endif %}

{{ enable_html_hooks('breadcrumb') }}
<ul id="{{ breadcrumbContainerId }}"
    class="breadcrumb {{ app.isAjax ? 'hidden' : '' }}">
    {% if breadcrumb is defined %}
    {% for index, breadcrumbPart in breadcrumb %}
    <li class="breadcrumb-item level-{{ index }} {{ breadcrumbPart.class|default('') }}">
        <a href="{{ breadcrumbPart.url }}"
           class="breadcrumb-link ajax-link">
            {{ app.translator.trans(breadcrumbPart.label, breadcrumbPart.labelParams|default([])) }}
        </a>
    </li>
    {% endfor %}
    {% endif %}
</ul>

{% if app.isAjax %}
<script>
    require(["jquery"], function ($) {
        // This breadcrumb is displayed in the layout #breadcrumb
        var breadcrumbToDisplaySelector = "#{{ breadcrumbContainerId }}";
        $(document).trigger("uiNeedsBreadcrumbUpdate", {
            fromSelector: breadcrumbToDisplaySelector
        });
    });
</script>
{% endif %}
-->
