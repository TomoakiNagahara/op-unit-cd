<?php
/** op-unit-cd:/CD.class.php
 *
 * @created    2024-04-12
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
namespace OP\UNIT;

/** use
 *
 */
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\IF_UNIT;

/** Include
 *
 */
require_once(__DIR__.'/CD_2024.trait.php');

/** CD
 *
 * @created    2024-04-12
 * @version    1.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
class CD implements IF_UNIT
{
	use OP_CORE, OP_UNIT, CD\CD_2024;
}
