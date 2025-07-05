<?php
/**	op-unit-cd:/CD.class.php
 *
 * @created    2024-04-12
 * @version    1.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara All right reserved.
 */

/**	Declare strict
 *
 */
declare(strict_types=1);

/**	namespace
 *
 */
namespace OP\UNIT;

/**	use
 *
 */
use OP\OP_CORE;
use OP\IF_CD;

/**	Include
 *
 */
require_once(__DIR__.'/CD.trait.php');
require_once(__DIR__.'/CD_2024.trait.php');

/**	CD
 *
 * @created    2024-04-12
 * @version    1.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara All right reserved.
 */
class CD implements IF_CD
{
	use OP_CORE, CD\CD, CD\CD_2024;
}
