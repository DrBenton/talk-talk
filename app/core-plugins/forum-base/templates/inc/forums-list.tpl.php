<?php if (!empty($this->forums)) : ?>

    <?= $this->hooks()->html('forums_list') ?>
    <div class="forums-list-container">
        <div class="forums-list">
            <?php foreach($this->forums as $forum): ?>
            <div class="forum-container level-{{ forum.depth }}">
                <div class="forum">
                    <h3 class="forum-title">
                        <a href="<?= $this->e($this->app()->path('forum-base/forum', array('forum' => $forum->id))) ?>"
                           class="forum-link root-forum-link ajax-link">
                            <?= $this->e($forum->title) ?>
                        </a>
                        <span class="badge"><?= $this->e($forum->nb_posts) ?></span>
                    </h3>
                    <div class="content">
                        <div class="forum-description">
                            <?= $this->e($forum->desc) ?>
                        </div>
                        <?php if ($forum->hasChild()): ?>
                            <div class="forum-children">
                                <?php foreach ($forum->getChildren() as $childForum): ?>
                                <h5>
                                    <a href="<?= $this->e($this->app()->path('forum-base/forum', array('forum' => $childForum->id))) ?>"
                                       class="forum-link child-forum-link ajax-link">
                                        <?= $this->e($childForum->title) ?>
                                    </a>
                                    <span class="badge"><?= $this->e($childForum->nb_posts) ?></span>
                                </h5>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div><?php /* end .content */ ?>
                </div><?php /* end .forum */ ?>
            </div><?php /* end .forum-container */ ?>
            <?php endforeach ?>
        </div><?php /* end .forums-list */ ?>
    </div><?php /* end .forums-list-container */ ?>

<?php else: ?>

    <div class="empty-list-msg">
        <?= $this->transE('core-plugins.forum-base.forums-list.no-forum') ?>
    </div>

<?php endif ?>
