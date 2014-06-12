<?php if ($this->nbPages > 1): ?>
    <?= $this->hooks()->html('component.pagination') ?>
    <ul class="pagination">
        <li class="first-page <?= ($this->currentPageNum === 1) ? 'disabled' : '' ?>">
            <a href="<?= str_replace('%page%', 1, $this->baseUrl) ?>" class="ajax-link">&laquo;</a>
        </li>
        <?php for($pageNum = 1; $pageNum <= $this->nbPages; $pageNum++ ): ?>
            <li class="page-num <?= ($this->currentPageNum === $pageNum) ? 'disabled' : '' ?>">
                <a href="<?= str_replace('%page%', $pageNum, $this->baseUrl) ?>" class="ajax-link"><?= $pageNum ?></a>
            </li>
        <?php endfor ?>
        <li class="last-page <?= ($this->currentPageNum === $this->nbPages) ? 'disabled' : '' ?>">
            <a href="<?= str_replace('%page%', $this->nbPages, $this->baseUrl) ?>" class="ajax-link">&raquo;</a>
        </li>
    </ul>
<?php endif ?>
