<?php
/** op-unit-cd:/function/Display.php
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

/** Display message
 *
 * @created    2024-04-12
 * @param      string      $message
 */
function Display($message)
{
	//	...
	static $_display;

	//	...
	if( empty($_display) ){
		$_display = OP()->Request('display');
	}

	//	...
	if( empty($_display) ){
		return;
	}

	//	...
	$meta_path = OP()->MetaPath( getcwd() );
	echo "{$meta_path} - {$message}\n";
}
