<?php
/** op-unit-cd:/include/PathList.php
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

//	...
$git_root = OP()->MetaPath('git:/');

//	...
$_list[] = $git_root.'asset/core/';
$_list[] = $git_root.'asset/develop/';

//	...
$temp = [
	'unit',
	'layout',
	'module',
	'webpack',
];

//	...
foreach( $temp as $dir ){
	//	...
	foreach( glob("{$git_root}asset/{$dir}/*") as $path ){
		$_list[] = $path;
	}
}

//	asset
$_list[] = $git_root.'asset/develop/';
$_list[] = $git_root.'asset/bootstrap/';
$_list[] = $git_root.'asset/git/';

//	...
$_list[] = $git_root;

//	...
return $_list;
