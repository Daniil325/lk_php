<div class="form_container">
    <h2 class="form_title"><?php echo $this->title; ?></h2>
    <form id="formDataForm" action="<?php echo htmlspecialchars($this->action); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" value="" />
        <?php foreach ($this->fields as $field):
            $field->render();
        endforeach; ?>
        <button class="form_button" type="submit"><?php echo htmlspecialchars($this->buttonText); ?></button>
    </form>
</div>