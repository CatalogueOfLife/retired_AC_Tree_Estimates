<?php
	$keys = array(
		'1YND3HOk9mjEGV4F4TP-SGxr4v6aPdH_eJTOOTLro-o4'
	);
	
	foreach ($keys as $key) {
		$url = "https://docs.google.com/spreadsheets/d/{$key}/export?format=csv&id={$key}&gid=0";
		if (($handle = fopen($url, "r")) !== false) {
			$i = 0;
			$data = $header = $row = array();
			while (($r = fgetcsv($handle, 0, ",")) !== false) {
				foreach ($r as $k => $v) {
					$i == 0 ? $header[$k] = $v : $row[$header[$k]] = $v;
				}
				if ($i > 0) $data[] = $row;
				$i++;
			}
			fclose($handle);
		}
		print_r($data);
	}
?>