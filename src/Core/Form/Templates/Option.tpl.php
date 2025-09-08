<div class="form_option">
    <input
        type="<?php echo htmlspecialchars($type); ?>"
        name="<?php echo htmlspecialchars($name); ?>"
        value="<?php echo htmlspecialchars($this->value); ?>"
        id="<?php echo htmlspecialchars($this->id); ?>"
        <?php if ($this->checked): ?>checked<?php endif; ?>
        <?php if (!empty($this->validate)): ?>
        data-validate="<?php echo htmlspecialchars($this->validate); ?>"
        <?php endif; ?> />


    <label class="label" for="<?php echo htmlspecialchars($this->id); ?>">
        <?php echo htmlspecialchars($this->label); ?>
    </label>
</div>