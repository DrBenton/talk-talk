<input type="hidden" name="<?= $this->app()->get('csrf')->getTokenName() ?>"
       value="<?= $this->app()->get('csrf')->getTokenValue() ?>">