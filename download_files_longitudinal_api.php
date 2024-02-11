<?php

#### The purpose of this script is to download all files in a longitudinal project by identifying which fields are file type and have files stored (includes repeated instruments)

#### Add your REDCap API URL e.g. https://redcap.com/api/ ####
$redcapurl = "YOUR REDCAP API URL";

#### Add your project API token e.g. BDB2C1E61296977A15FF ####
$token = "YOUR TOKEN HERE";

#### Download Metadata in order to identify file entries####
$data = array(
    'token' => $token,
    'content' => 'metadata',
    'format' => 'json',
    'returnFormat' => 'json'
);
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
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
$output = curl_exec($ch);
$obj = json_decode($output,true);
curl_close($ch);
# Count the array size
$objnum=count($obj);

$k=0;
$fieldnames=array();

# Create a table with the field names
for($i=0;$i<$objnum;$i++){
	if($obj[$i]["field_type"]=="file"){
		$fieldnames[$k]=$obj[$i]["field_name"];
		$k++;
	}
}
print_r($fieldnames);
$fieldnamesnum=count($fieldnames);

for($i=0;$i<$fieldnamesnum;$i++){
	sleep(1);
	#### Export the records and file names ####
	$data = array(
		'token' => $token,
		'content' => 'record',
		'action' => 'export',
		'format' => 'json',
		'type' => 'flat',
		'csvDelimiter' => '',
		'fields' => array('study_id',$fieldnames[$i]),
		'rawOrLabel' => 'raw',
		'rawOrLabelHeaders' => 'raw',
		'exportCheckboxLabel' => 'false',
		'exportSurveyFields' => 'false',
		'exportDataAccessGroups' => 'false',
		'returnFormat' => 'json'
	);
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
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
	$output = curl_exec($ch);
	curl_close($ch);
	
	$names=json_decode($output,true);
	$namesnum=count($names);
	print_r($names);
	
	for($n=0;$n<$namesnum;$n++){
		# Check if there are repeat instances
		if(isset($names[$n]["redcap_repeat_instance"])){
			# Check if the readcap_repeat_instance is empty thus that instrument is not repeatable and check that the fieldname is populated. Always use trim to remove spaces
			if(trim($names[$n]["redcap_repeat_instance"])==NULL && trim($names[$n][$fieldnames[$i]])!=NULL){
				# Extract the files with correct names
				$data = array(
					'token' => $token,
					'content' => 'file',
					'action' => 'export',
					'record' => $names[$n]['study_id'],
					'field' => $fieldnames[$i],
					'event' => $names[$n]['redcap_event_name'],
					'returnFormat' => 'json'
				);
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
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
				$output = curl_exec($ch);
				curl_close($ch);
				
				
				# Do not include the redcap_repeat_instance separator
				if($output!="{\"error\":\"There is no file to download for this record\"}"){
					$outname=$names[$n]["study_id"]."___".$fieldnames[$i]."___".$names[$n]['redcap_event_name']."___".$names[$n][$fieldnames[$i]];
					$fp = fopen("files/$outname", 'w');
					fwrite($fp, $output);
					fclose($fp);
					echo "Downloaded $outname\n";
				}
				# Check that the fieldname is populated. Always use trim to remove spaces
			}elseif(trim($names[$n][$fieldnames[$i]])!=NULL){
				# Extract the files with correct names
				$data = array(
					'token' => $token,
					'content' => 'file',
					'action' => 'export',
					'record' => $names[$n]['study_id'],
					'field' => $fieldnames[$i],
					'repeat_instance' => $names[$n]["redcap_repeat_instance"],
					'event' => $names[$n]['redcap_event_name'],
					'returnFormat' => 'json'
				);
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
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
				$output = curl_exec($ch);
				curl_close($ch);
				
				
				if($output!="{\"error\":\"There is no file to download for this record\"}"){
					$outname=$names[$n]["study_id"]."___".$names[$n]["redcap_repeat_instance"]."___".$fieldnames[$i]."___".$names[$n]['redcap_event_name']."___".$names[$n][$fieldnames[$i]];
					$fp = fopen("files/$outname", 'w');
					fwrite($fp, $output);
					fclose($fp);
					echo "Downloaded $outname\n";
				}
			}
		# Check that the fieldname is populated. Always use trim to remove spaces
		}elseif(trim($names[$n][$fieldnames[$i]])!=NULL){
			$data = array(
				'token' => $token,
				'content' => 'file',
				'action' => 'export',
				'record' => $names[$n]['study_id'],
				'field' => $fieldnames[$i],
				'event' => $names[$n]['redcap_event_name'],
				'returnFormat' => 'json'
			);
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
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
			$output = curl_exec($ch);
			curl_close($ch);
			
			
			if($output!="{\"error\":\"There is no file to download for this record\"}"){
				$outname=$names[$n]["study_id"]."___".$fieldnames[$i]."___".$names[$n]['redcap_event_name']."___".$names[$n][$fieldnames[$i]];
				$fp = fopen("files/$outname", 'w');
				fwrite($fp, $output);
				fclose($fp);
				echo "Downloaded no repeat $outname\n";
			}
		}
	}
}

?>