<?php
/** op-unit-cd:/CD.trait.php
 *
 * @created    2024-04-21
 * @version    1.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/** Declare strict
 *
 */
declare(strict_types=1);

/** namespace
 *
 */
namespace OP\UNIT\CD;

/** use
 *
 */

trait CD
{
	/** CI
	 *
	 * @created    2023-02-17
	 * @return    \OP\UNIT\CI
	 */
	static private function CI()
	{
		return OP()->Unit('CI');
	}

	/** Git
	 *
	 * @created    2023-02-05
	 * @return    \OP\UNIT\Git
	 */
	static private function Git()
	{
		return OP()->Unit('Git');
	}
}
