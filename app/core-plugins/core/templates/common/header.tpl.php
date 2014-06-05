<?= $this->hooks()->html('header') ?>
<header>
    <h1><a href="<?= $this->app()->path('core/home') ?>" class="ajax-link">{{ app['forum-base.title'] }}</a></h1>
    <h1>Talk-Talk</h1>

    <nav>
        <ul>
            <?php /* will be filled by Plugins */ ?>
        </ul>
    </nav>

    <div id="logged-user-container">
        <?php if ($this->app()->vars['isAuthenticated']): ?>
            <?php $this->insert('auth::common/user-display') ?>
        <?php endif ?>
    </div>

</header>
