<div class="form_field">
    <label class="label" for="<?php echo htmlspecialchars($this->id); ?>">
        <?php echo htmlspecialchars($this->label); ?>
        <?php if ($this->required): ?>
            <span class="required">*</span>
        <?php endif; ?>
    </label>

    <input
        type="<?php echo htmlspecialchars($this->type); ?>"
        name="<?php echo htmlspecialchars($this->id); ?>"
        value="<?php echo htmlspecialchars($this->value); ?>"
        id="<?php echo htmlspecialchars($this->id); ?>"
        <?php if ($this->required): ?>required<?php endif; ?>
        <?php if (!empty($this->validate)): ?>
        data-validate="<?php echo htmlspecialchars($this->validate); ?>"
        <?php endif; ?> />
</div>