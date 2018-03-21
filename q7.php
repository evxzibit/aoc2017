<?php

/**
 * Read file from the comand line
 * @param  string $path The name of the file to read(sample_puzzle.txt)
 * @return array Array of data read from the file
 */
	function readTheFile($path) {
	    $handle = fopen($path, "r");
	    $data = [];
	    $parentNameAndWeight = [];
	    $childNameAndWeight = [];
	    while(!feof($handle)) {
	    	$line = trim(fgets($handle));
	        if (strpos($line, '->') !== false) { // check if has children
	        	$lineWithChildren = array_map('trim', explode('->', $line));
	        	$lineWithChildrenParts = array_map('trim', explode(' ' , $lineWithChildren[0]));
	        	$nameOnly = $lineWithChildrenParts[0];
	        	$weight = str_replace(array('(', ')'), '', $lineWithChildrenParts[1]);

	        	$data[] = $nameOnly;
	        	$explodedChildren = array_map('trim', explode(',', $lineWithChildren[1]));
	        	$parentNameAndWeight[$nameOnly]['weight'] = $weight;
	        	$parentNameAndWeight[$nameOnly]['name'] = $nameOnly;
	        	$parentNameAndWeight[$nameOnly]['children'] = $explodedChildren;
	        	$data = array_merge($data, $explodedChildren);
	        } else { // lines with names and weight
	        	$lineParts = array_map('trim', explode(' ' , $line));
	        	$nameOnly = $lineParts[0];
	        	$weight = str_replace(array('(', ')'), '', $lineParts[1]);
	        	$childNameAndWeight[$nameOnly] = $weight;
	        	$data[] = $nameOnly;
	        }
	    }
	    
	    fclose($handle);

	    return [
	    	'data' => $data, 
	    	'parentNameAndWeight' => $parentNameAndWeight, 
	    	'childNameAndWeight' => $childNameAndWeight
	    ];
	}

/**
 * Gets the bottom program name
 * @param  array $data the data array read from the file
 * @return string       The bottom program name
 */
	function getBottomProgramName($data) {
		$dataCount = array_count_values($data); //get total count per name

		$dataMinCount = array_keys($dataCount, min($dataCount)); //get the name with the minimum count(this is the bottom program)
		return $dataMinCount[0];
	}

/**
 * Gets the weight needed to balance
 * @param  array $data     Data array from the file
 * @param  string $progName The bottom program name
 * @return integer           The weight needed to balance the program
 */
	function getNeededBalancedWeight($data, $progName) {
		unset($data['parentNameAndWeight'][$progName]);

	 	foreach ($data['parentNameAndWeight'] as &$dataArr) {
	 		$dataArr['totalChildWeight'] = 0;
	 		foreach ($dataArr['children'] as $value) {
	 			if (isset($data['childNameAndWeight'][$value])) {
	 				$dataArr['totalChildWeight'] += $data['childNameAndWeight'][$value]; //total weight per child
	 			}
	 		}
	 		$dataArr['totalWeight'] = $dataArr['totalChildWeight'] + $dataArr['weight']; //total weight per parent
	 	}

		$totalWeights = array_column($data['parentNameAndWeight'], 'totalWeight', 'name'); //gets an array of key value pair of the total weights

		$minTotalWeightKey = array_keys($totalWeights, min($totalWeights))[0]; 

		$maxTotalWeightKey = array_keys($totalWeights, max($totalWeights))[0];

		$maxMinDifference = $data['parentNameAndWeight'][$maxTotalWeightKey]['totalWeight'] - $data['parentNameAndWeight'][$minTotalWeightKey]['totalWeight'];//the difference between the max and min weights


		$neededBalancedWeight = $data['parentNameAndWeight'][$maxTotalWeightKey]['weight'] - $maxMinDifference;

		return $neededBalancedWeight;
	}

    $data = readTheFile($argv[1]);
    $progName = getBottomProgramName($data['data']);
    $neededBalancedWeight = getNeededBalancedWeight($data, $progName);

    echo "\n The bottom program name is : " . $progName . "\n";
    echo "\n The weight needed is : " . $neededBalancedWeight . "\n";