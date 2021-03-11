# Dicebag
PHP port of [Dicebag](https://github.com/8bitskull/dicebag) by [8bitskull](https://github.com/8bitskull). Dicebag is a class of probability functions designed specifically for games.

Inspired by this excellent blog post: https://www.redblobgames.com/articles/probability/damage-rolls.html

## Other Ports
[dicebag-c#](https://github.com/Efmi/dicebag-csharp) by [Efmi](https://github.com/Efmi)

[dicebag-godot](https://github.com/Yagich/dicebag-godot) by [Yagich](https://github.com/Yagich)

## Install
Well, I'll make it so you can composer install this later. Right now just drop into projects and include like a normal php file. Namespace it if you already have a dicebag class.

```
include dicebag.php;
$dicebag = new Dicebag();
print($dicebag->flipCoin());
```
# Usage
mt_rand is used for the internal randomness function so you can seed it using mt_srand(seed);
### dicebag->flipCoin()
Flip a coin.

**RETURNS**
* `result` (boolean) - true or false (50% chance).

### dicebag->rollDice($num_dice, $num_sides, $modifier)
Roll a number of dice, D&D-style. An example would be rolling 3d6+2. Returns the sum of the resulting roll.
```
$dicebag->rollDice(3,6,2);
```

**PARAMETERS**
* `num_dice` (number) - Number of dice to roll.
* `num_sides` (number) - Number of sides on the dice.
* `modifier` (number) - Number to add to the result.

**RETURNS**
* `result` (number) - Sum of rolled dice plus modifier.

### dicebag->rollSpecialDice($numOfDice, $numOfResults, $advantage, $numOfSides)
Roll a number of dice and choose one (or more) of the highest (advantage) or lowest (disadvantage) results. Returns the sum of the relevant dice rolls.

**PARAMETERS**
* `numOfDice` (number) - Number of dice to roll.
* `numOfResults` (number) - How many of the highest (advantage) or lowest (disadvantage) dice to sum up.
* `advantage` (boolean) - If true, the highest rolls will be selected, otherwise the lowest values will be selected.
* `numOfSides` (number) - Number of sides on the dice.

**RETURNS**
* `result` (number) - Sum of the highest (advantage) or lowest (disadvantage) dice rolls.

### dicebag->rollCustomDice(array $sides, $numOfDice, $returnRolls)
Roll custom dice. The dice can have sides with different weights and different values. So it's really rolling weighted dice.

**PARAMETERS**
* `sides` (array) - An array describing the sides of the die in the format `[[weight1, value1], [weight2, value2] ...]`. Note that the values must be numbers.
* `numOfDice` (number) - How many dice to roll.
* `returnRolls` (boolean) - If you would like to return an array of the rolls with the value.

**RETURNS**
* `value` (any) - The sum of the values as specified in array `sides` OR an `array` containing the value and the rolls in this format: `[value, [roll1, roll2,...]]`

### dicebag->createBag($id, $num_success, $num_fail, $reset_on_success)
Create a marble bag of green (success) and red (fails) 'marbles'. This allows you to, for example, make an unlikely event more and more likely the more fails are accumulated.

**PARAMETERS**
* `id` (string, number, hash) - A unique identifier for the marble bag.
* `num_success` (number) - The number of success marbles in the bag.
* `num_fails` (number) -  The number of fails marbles in the bag.
* `reset_on_success` (boolean) - Whether or not the bag should reset when a successful result is drawn. If false or nil the bag will reset when all marbles have been drawn.

### dicebag->bagDraw($id)
Draw a marble from a previously created bag.

**PARAMETERS**
* `id` (string, number, hash) - A unique identifier for the marble bag, default 1.

**RETURNS**
* `result` (boolean)

### dicebag->bagReset($id)
Manually reset a marble bag. Will also be called when a marble bag is empty, or a success is drawn in a bag where `reset_on_success` is true.

**PARAMETERS**
* `id` (string, number, hash) - A unique identifier for the marble bag.

### dicebag->createTable($id, $rollable_table)
Create a rollable table. This is similar to a marble bag, except each entry can have a different weight, and can return any value (not just a boolean).

**PARAMETERS**
* `id` (string, number, hash) - A unique identifier for the rollable table.
* `rollable_table` (array) - An array of weights, values and reset flags.

Array `rollable_table` has the format: `[[weight1, value1, reset_on_roll1], [weight2, value2, reset_on_roll2], ...]` where the parameters are:
* `weight` (number) - The relative probability of drawing the value.
* `value` (any) - The value to be returned if drawn.
* `reset_on_roll` (boolean) - Whether or not the table should be reset when this value is drawn. If all of these are false, the table will reset when all values have been drawn.

### dicebag->tableRoll($id)
Draw a random value from the rollable table created in dicebag->createTable(). The value will be removed from the table. If `reset_on_roll` is true, the table will reset. Otherwise, the table will reset when all values are drawn.

**PARAMETERS**
* `id` (string, number, hash) - A unique identifier for the rollable table. Defaults to 1.

**RETURNS**
* `value` (any) - The value specified in dicebag.table_create.

### dicebag->tableReset($id)
Manually reset a rollable table. Will also be called when the rollable table is empty, or a drawn value where `reset_on_roll` is true.

**PARAMETERS**
* `id` (string, number, hash) - A unique identifier for the rollable table.
