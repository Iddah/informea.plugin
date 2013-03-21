<h2>Import whc sites from XML dump</h2>

Steps:
<br/>
1. Go to <a target="_blank" href="http://whc.unesco.org/en/list/xml/">http://whc.unesco.org/en/list/xml/</a>
<br/>
2. Save the XML locally
<br/>
<br/>
3. Upload the file in the form below, and press "Test" first to make a simulation
<br/>
4. Upload the file again and press "Just do it!"

<p class="form">
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="page" value="informea_miscellaneous"/>
    <input type="hidden" name="act" value="import_whc_xml"/>
    <input type="file" name="file"/>

    <input type="submit" name="actioned" value="Test it" class="button"/>
    <input type="submit" name="actioned" value="Just do it!" class="button-primary"/>
</form>

<?php
if ($page_data->actioned && $page_data->success) {
    ?>
    <h3>Sites (<?php echo count($page_data->sites); ?>)</h3>

    <table>
        <thead>
        <tr>
            <th style="width: 40px;">Id</th>
            <th>Site name</th>
            <th>Country code</th>
            <th style="width: 100px;">Latitude</th>
            <th style="width: 100px;">Longitude</th>
            <th>URL</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($page_data->sites as $site) { ?>
            <tr>
                <td><?php echo $site['original_id']; ?></td>
                <td><?php echo $site['name']; ?></td>
                <td><?php echo $site['id_country']; ?></td>
                <td><?php echo $site['latitude']; ?></td>
                <td><?php echo $site['longitude']; ?></td>
                <td><a target="_blank" href="<?php echo $site['url']; ?>">visit</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
</p>
