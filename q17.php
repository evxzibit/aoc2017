<?php

/**
 * Get the value after 2017 insertions of a specific number of steps
 * @param  integer $steps Number of steps/iteration
 * @return integer        Value after 2017 insertions of $steps steps
 */
    function spinlock($steps) {
    	$insertions = 2017;
    	$currentPosition = 0;
    	$circularBuffer = [0];
    	for ($i = 1; $i < $insertions +1; $i++) {
    		$currentPosition = ($currentPosition + $steps) % count($circularBuffer) + 1; // new current currentPosition after iterations
    		array_splice($circularBuffer, $currentPosition, 0, [$i]); //remove new currentPosition and replace it
    	}

    	return $circularBuffer[$currentPosition + 1];
    }

/**
 * Gets the value after zero and 50000000 insertions
 * @param  integer $steps Number of steps/iteration
 * @return interger        Value after Zero
 */
    function spinlockPartTwo($steps) {
		$valueAfterZero = 0;
		$insertions = 50000000;
		$currentPosition = 0;
		for ($i = 1; $i <= $insertions; $i++) {
			$currentPosition = (($currentPosition + $steps) % $i) + 1;
			if ($currentPosition == 1) { //assign current value after 0 if the current position is 1
				$valueAfterZero = $i; 
			}
			$positionSkipped = floor(($i - $currentPosition) / $steps);
			$currentPosition = ($currentPosition + ($steps * $positionSkipped) + $positionSkipped);
			$i += $positionSkipped;
		}

		return $valueAfterZero;
    }

        $value = spinlock($argv[1]);

    	echo "Part1 result: " . $value . "\n";

        $valueAfterZero = spinlockPartTwo($argv[1]);

        echo "Part2 result: " . $valueAfterZero . "\n";

