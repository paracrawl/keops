<form action="/sentences/search.php" class="form-inline">
    <div class="form-group">
        <input type="hidden" id="task_id" name="task_id" value="<?= $task->id ?>">
        <input class="form-control" id="search-term" name="term" value="<?php if (isset($search_term)) { echo $search_term; } ?>" placeholder="Search through sentences">

        <?php
            $labels = array("L", "A", "T", "MT", "F", "V", "P");
        ?>

        <select class="form-control" name="label">
        <option value="ALL">Everything</option>
            <?php
                foreach ($labels as $labelcode) { ?>
                    <option value="<?php echo $labelcode; ?>" <?php if (isset($label) && $labelcode == $label) { echo "selected"; } ?>><?php echo $labelcode; ?> label</option>
                <?php }
            ?>
        </select>

        <button type=submit class="btn btn-primary" id="search-term-button">Search</button>
    </div>
</form>