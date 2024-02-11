<?php

#### Scan the directory with files to be uploaded from the download_files_api.php script, because this script needs the naming convection from download_files_api.php. It includes repeated measurements.

#### Add the full path to the files e.g. /redcap/data/files
$dir=scandir("YOUR FULL PATH TO FILES");
$dirnum=count($dir);

#### Add your REDCap API URL e.g. https://redcap.com/api/ ####
$redcapurl = "YOUR REDCAP API URL";

#### Add your project API token e.g. BDB2C1E61296977A15FF ####
$tokenupload = "YOUR TOKEN HERE";

for($i=2;$i<$dirnum;$i++){
	# Extract the files with correct names
	$exp=explode("___",$dir[$i]);
	$expnum=count($exp);
	if($expnum==4){
		$data = array(
			'token' => $tokenupload,
			'content' => 'file',
			'action' => 'import',
			'record' => $exp[0],
			'field' => $exp[2],
			'repeat_instance' => $exp[1],
			'returnFormat' => 'json'
		);
		
		
		$data['file'] = (function_exists('curl_file_create') ? curl_file_create("files/$dir[$i]", 'image/jpeg', "$dir[$i]") : "@/tmp/$dir[$i]");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $redcapurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		print $output;
		curl_close($ch);
		
		echo "Uploaded repeat $dir[$i]\n";
	}elseif($expnum==3){
		$data = array(
			'token' => $tokenupload,
			'content' => 'file',
			'action' => 'import',
			'record' => $exp[0],
			'field' => $exp[1],
			'returnFormat' => 'json'
		);
		
		
		$data['file'] = (function_exists('curl_file_create') ? curl_file_create("files/$dir[$i]", 'image/jpeg', "$dir[$i]") : "@/tmp/$dir[$i]");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $redcapurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		print $output;
		curl_close($ch);
		
		echo "Uploaded $dir[$i]\n";
	}
}



?>