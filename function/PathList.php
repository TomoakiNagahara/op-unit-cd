<?php
/** op-unit-cd:/function/PathList.php
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
namespace OP\UNIT\CD;

/** Generate submodules path list.
 *
 * @created    2024-04-12
 * @param      string      $message
 */
function PathList()
{
	//	...
	static $_list;

	//	...
	if( empty($_list) ){
		$include = function(){ return include(__DIR__.'/../include/PathList.php'); };
		$_list = $include();
	}

	//	...
	return $_list;
}
