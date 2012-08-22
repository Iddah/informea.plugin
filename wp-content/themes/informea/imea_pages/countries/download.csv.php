<?php
	$language = get_request_value('lang', 'en');
	$split = ('en' == $language) ? ',' : ';';
	$newline = "\r\n";
	$page_data = new imea_countries_page(null);
	$data = $page_data->index_grid();
	$columns = $data['column'];
	$countries = $data['countries'];
	$signatures = $data['signatures'];

	$filename = 'informea_countries_' . date('d_M_Y_H_i') . '.csv';
	header('Content-type: text/csv');
	header("Content-disposition: attachment;filename=$filename");
	$line = 'Country';
	$csv = '';
	foreach ($columns as $column){
		$line .= $split.$column->short_title;
	}
	$line .= $newline;
	echo ($line);

	foreach ($countries as $country){
		$line = $country->name;
		foreach($columns as $column) {
			$id_treaty = $column->id;
			$id_country = $country->id;
			$coldata = '';
			if(isset($signatures[$id_treaty])) {
				$tmparr = $signatures[$id_treaty];
				if(isset($tmparr[$id_country])) {
					$coldata = $tmparr[$id_country];
				}
			}
			$line .= $split.$coldata;
		}
		$line .= $newline;
		echo ($line);
	}
?>
