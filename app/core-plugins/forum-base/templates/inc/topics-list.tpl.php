<?php if (!empty($this->topics)): ?>

    <?= $this->hooks()->html('topics_list', 'topic') ?>
    <div class="topics-list-container">
        <div class="topics-list">
            <div class="topics-list-desc list-desc">
                <?= $this->nbTopicsTotal ?> topics - page <?= $this->paginationData['currentPageNum'] ?> -
                <?= count($this->topics) ?> topics on this page
            </div>
            <?php foreach ($this->topics as $topic): ?>
                <div class="topic-container">
                    <div class="topic">
                        <h4 class="topic-title">
                            <a href="<?= $this->app()->path('forum-base/topic', array('topic' => $topic->id)) ?>"
                               class="ajax-link">
                                <?= $this->e($topic->title) ?> - <?= $topic->nb_replies ?> replies
                            </a>
                        </h4>

                        <div class="topic-info">
                            Created <?= $topic->created_at ?> by <span class="topic-author-name"><?= $topic->author->login ?></span><br>
                            Last modified <?= $topic->updated_at ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div><?php /* end .topics-display */ ?>
    </div><?php /* end .topics-display-container */ ?>

<?php else: ?>

    <div class="empty-list-msg">
        <?= $this->transE('core-plugins.forum-base.topics-list.no-topic') ?>
    </div>

<?php endif ?>
