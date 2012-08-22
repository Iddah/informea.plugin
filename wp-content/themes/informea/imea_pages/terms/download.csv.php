<?php
	$language = get_request_value('lang', 'en');
	$separator = ('en' == $language) ? ',' : ';';
	$imea_terms = new Thesaurus(null);
	$themes = $imea_terms->get_top_concepts();
	$cc = count($themes);
	// Find out the maximul depth level for terms
	$max_level = 0;
	$level = 0;
	foreach($themes as $idx => $theme) {
		$imea_terms->get_narrower_recursive($theme, $level, function($term, $level) {
			global $max_level;
			if($level > $max_level) { $max_level = $level; }
		});
	}
	$max_level++;

	// header('Content-type: text/plain');
	$filename = 'informea_vocabulary_' . date('d_M_Y_H_i') . '.csv';
	header('Content-type: text/csv');
	header("Content-disposition: attachment;filename=$filename");

	// Generate the CSV header
	$columns = array();
	for($i = 0; $i < $max_level; $i++) {
		$columns[] = 'Term L' . ($i + 1);
	}
	$columns[] = 'Term short version';
	$columns[] = 'Synonyms';
	$columns[] = 'Related terms';

	$cc = count($columns);
	foreach($columns as $idx => $column) {
		echo $column;
		if($idx < $cc - 1) { echo $separator; }
	}
	echo "\n";
	// Header done, put the themes
	// First get the terms to find out the maximum depth level
	$level = 0;
	foreach($themes as $idx => $theme) {
		echo $imea_terms->csv_write_row($theme, $level, $max_level, $separator);

		// Write their children
		$level = 0;
		$imea_terms->get_narrower_recursive($theme, $level, function($term, $level) use ($imea_terms, $max_level, $separator) {
			echo $imea_terms->csv_write_row($term, $level, $max_level, $separator);
			// echo the term according to the level
			//for($i = 0; $i < $level; $i++) { echo "\t"; }
			//echo $term->term . "\n";
		});
	}
?>
