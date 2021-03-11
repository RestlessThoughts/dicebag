<?php

class Dicebag
{
	public $bags;
	
	public $tables;

	// D&D style dice roll, for example 3d6+2. Returns resulting roll value.
	public function rollDice($num_dice=1, $num_sides=6, $modifier=0)
	{
		$result = $modifier;

		for ($i=1;$i<=$num_dice;$i++)
		{
			$result += mt_rand(1,$num_sides);
		}
		return $result;
	}

	/* Roll one or more dice with advantage or disadvantage 
	(if advantage is not true roles are disadvantages).
	Returns the num_results sum of the highest (advantage) or lowest (disadvantage) value of all rolls.
	*/
	public function rollSpecialDice($numOfDice=2, $numOfResults=1, $advantage=true,$numOfSides=6)
	{
		$rolls = [];

		for ($i=0;$i<$numOfDice;$i++)
		{
			$r = mt_rand(1,$numOfSides);
			$rolls[] = $r;
		}

		if ($advantage)
			rsort($rolls);
		else
			sort($rolls);

		$result = array_slice($rolls, 0, $numOfResults);
		return array_sum($result);
	}

	/*
	Roll one or more custom dice. 
	Parameter $sides is an array in the format [[weight1, value1], [weight2, value2] ...]. Returns the total value of rolled custom dice and optionally the rolls themselves.
	*/
	public function rollCustomDice(array $sides, $numOfDice=1, $returnRolls = false)
	{
		$result = 0;
		$total_weight = 0;
		$num_sides = count($sides);
		$rolls = [];

		for($i=0;$i<$num_sides;$i++)
		{
			$total_weight += $sides[$i][0];
		}

		$weight_result = 0;

		for ($d=0;$d<$numOfDice;$d++)
		{
			$weight_result = mt_rand(0,$total_weight);

			$processed_weight = 0;
			for ($i=0;$i<$num_sides;$i++)
			{
				if ($weight_result <= ($sides[$i][0] + $processed_weight))
				{
					$result += $sides[$i][1];
					$rolls[] = $sides[$i][1];
					break;
				}

				$processed_weight += $sides[$i][0];
			}
		}

		if ($returnRolls)
			return [$result,$rolls];
		return $result;
	}

	/*
	Create a bag of green (success) and red (fail) "marbles" that you can draw from. If reset_on_success is true, the bag will be reset after the first green (success) marble is drawn, otherwise the bag will reset when all marbles have been drawn.
	*/
	public function createBag($id=1,$num_success=1,$num_fail=1,$reset_on_success = true)
	{
		$this->bags[$id] = [
			'success' => $num_success,
			'fail' => $num_fail,
			'full_success' => $num_success,
			'full_fail' => $num_fail,
			'reset_on_success' => $reset_on_success
		];
	}

	// Draw a marble from marble bag id. 
	// Returns true or false.
	public function bagDraw($id=1)
	{
		if (!isset($this->bags[$id]))
			return false;

		$bag = $this->bags[$id];
		$result = mt_rand(1, $bag['success'] + $bag['fail']);

		if ($result > $bag['fail'])
		{
			$result = true;
			if ($bag['reset_on_success'])
				$this->bagReset($id);
			else
				$this->bags[$id]['success'] = $this->bags[$id]['success'] - 1;
		}
		else
		{
			$result = false;
			$this->bags[$id]['fail'] = $this->bags[$id]['fail'] - 1;
		}

		if ($this->bags[$id]
			['success']+$this->bags[$id]['fail'] <= 0)
			$this->bagReset($id);

		return $result;
	}

	// Refill a marble bag to its default number of marbles.
	public function bagReset($id)
	{
		if (!isset($this->bags[$id]))
			return;

		$this->bags[$id]['success'] = $this->bags[$id]['full_success'];
		$this->bags[$id]['fail'] = $this->bags[$id]['full_fail'];
	}

	/*
	Create a rollable table where entries are removed as they are rolled. 
	Parameter rollable table format: [[weight1, value1, reset_on_roll1], [weight2, value2, reset_on_roll2], ...]
	*/
	public function createTable($id, $rollable_table)
	{
		$this->tables[$id] = [
			'active' => $rollable_table,
			'original' => $rollable_table,
		];
	}

	/*
	Roll a value from a rollable table. 
	Returns the value specified in the table.
	*/
	public function tableRoll($id)
	{
		if (!isset($this->tables[$id]))
			return false;

		$target_table = $this->tables[$id]['active'];
		$total_weight = 0;
		$num_entries = count($target_table);

		// Count up the total weight
		for ($i = 0; $i < $num_entries; $i++)
		{
			$total_weight += $target_table[$i][0];
		}

		$weight_result = mt_rand(1,$total_weight);

		/// Find and return the resulting value
		$result = null;
		$processed_weight = 0;
		for ($i=0;$i<$num_entries;$i++)
		{
			if ($weight_result <= ($target_table[$i][0] + $processed_weight))
			{
				$result = $target_table[$i][1];

				if ($target_table[$i][2] or $num_entries == 1)
					$this->tableReset($id);
				else
				{
					//unset($this->tables[$id]['active'][$i]);
					array_splice($this->tables[$id]['active'],$i,1);
				}

				return $result;
			}
			else
			{
				$processed_weight += $target_table[$i][0];
			}
		}
	}

	public function tableReset($id)
	{
		if (!isset($this->tables[$id]))
			return;

		$this->tables[$id]['active'] = $this->tables[$id]['original'];

		return;
	}
}
