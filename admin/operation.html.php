<?php if ($page_data->actioned) { ?>
    <div class="updated settings-error" id="setting-error-settings_updated">
        <?php if ($page_data->success) { ?>
            <p><strong>Operation successful!</strong></p>
        <?php } else { ?>
            <p><strong>Error occurred!</strong>
            <ul>
                <?php foreach ($page_data->errors as $inpname => $inp_err) {
                    echo "<li>$inpname : $inp_err</li>";
                }?>
            </ul>
            </p>
        <?php } ?>
    </div>
<?php } ?>
