<?php
/**	op-unit-cd:/Rsync.class.php
 *
 * @created    2026-04-20
 * @license    Apache-2.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara
 */

/**	Declare strict type
 *
 */
declare(strict_types=1);

/**	Namespace
 *
 */
namespace OP\UNIT\CD;

/**	Use
 *
 */
use OP\OP_CORE;
use OP\OP_CI;
use OP\IF_CD;

/**	Rsync
 *
 * @created    2026-04-20
 */
class Rsync implements IF_CD
{
	/**	trait
	 *
	 */
	use OP_CORE, OP_CI;

	/**	Automatically
	 *
	 * @created    2026-04-20
	 */
	function Auto()
	{
		D();
	}
}
