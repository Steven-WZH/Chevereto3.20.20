<?php if (!defined('access') or !access) {
    die('This file cannot be directly accessed.');
} ?>
<input type="hidden" name="form-album-parent-id" value="<?php echo get_album()['id_encoded']; ?>">
<div class="c7 input-label">
	<?php
        $label = 'form-album-name';
    ?>
    <label for="<?php echo $label; ?>"><?php _se('Album name'); ?></label>
    <input type="text" name="<?php echo $label; ?>" class="text-input" value="" placeholder="<?php _se('Album name'); ?>" maxlength="<?php echo CHV\getSetting('album_name_max_length'); ?>" required>
</div>
<div class="input-label">
	<label for="form-album-description"><?php _se('Album description'); ?> <span class="optional"><?php _se('optional'); ?></span></label>
	<textarea id="form-album-description" name="form-album-description" class="text-input no-resize" placeholder="<?php _se('Brief description of this album'); ?>"></textarea>
</div>
<?php if (CHV\getSetting('website_privacy_mode') == 'public' or (CHV\getSetting('website_privacy_mode') == 'private' and CHV\getSetting('website_content_privacy_mode') == 'default')) {
        ?>
<div class="input-label overflow-auto">
    <div class="c7 grid-columns">
		<label for="form-privacy"><?php _se('Album privacy'); ?></label>
		<select name="form-privacy" id="form-privacy" class="text-input" data-combo="form-privacy-combo" rel="template-tooltip" data-tiptip="right" data-title="<?php _se('Who can view this content'); ?>">
			<?php
                $permissions = [
                    'public' 			=> ['label' => _s('Public')],
                    'private'			=> ['label' => _s('Private (just me)')],
                    'private_but_link'	=> ['label' => _s('Private (anyone with the link)')],
                    'password' 			=> ['label' => _s('Private (password protected)')],
                ];
        if (CHV\Login::isLoggedUser() == false) {
            unset($permissions['private']);
        }
        foreach ($permissions as $k => $v) {
            echo '<option value="'.$k.'">'.$v['label'].'</option>';
        } ?>
		</select>
	</div>
</div>
<div id="form-privacy-combo">
	<div data-combo-value="password" class="switch-combo soft-hidden">
		<div class="input-label overflow-auto">
			<div class="c7 grid-columns">
				<label for="form-album-password"><?php _se('Album password'); ?></label>
				<input type="text" name="form-album-password" class="text-input" value="">
			</div>
		</div>
	</div>
</div>
<?php
    } ?>