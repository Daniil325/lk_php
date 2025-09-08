<div>
    <fieldset class="form_fieldset" id="<?php echo htmlspecialchars($this->id); ?>" >
        <legend class="label"> <?php echo htmlspecialchars($this->legend); ?></legend>

        <?php foreach ($this->options as $option):
            $option->render($this->type, $this->name);
        endforeach; ?>
    </fieldset>
</div>