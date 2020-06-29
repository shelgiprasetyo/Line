<h3><img src="<?= $this->url->dir() ?>plugins/Line/Asset/line-icon.png" height="25px"/>&nbsp;Line</h3>
<div class="panel">
    <?= $this->form->label(t('Line Access Token'), 'line_access_token') ?>
    <?= $this->form->text('line_access_token', $values) ?>
    
    <p class="form-help">
        <a href="https://notify-bot.line.me/my/" target="_blank"><?= t('Create Line Access Token') ?></a>
        <br>
        <a href="https://github.com/shelgiprasetyo/Line" target="_blank"><?= t('Documentation') ?></a>
    </p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</div>
