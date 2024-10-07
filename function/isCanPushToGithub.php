<?php
/** op-unit-cd:/function/isCanPushToGithub.php
 *
 * @created    2024-09-14
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

/** Certain branch names cannot be pushed to GitHub.
 *
 * @created    2024-09-14
 * @param      string      $message
 */
function isCanPushToGithub(string $remote, string $branch) : bool
{
	//	...
	static $_config;

	//	...
	if(!$_config ){
		$_config = OP()->Config('cd');
	}

	//	...
	$url = ' '.trim(`git remote get-url {$remote}` ?? '');

	//	If the PUSH destination URL is GitHub.
	if( strpos($url, 'git@github.com:') or strpos($url, 'https://github.com/') ){
		//	To GitHub
	}else{
		//	Not to GitHub.
		return true;
	}

	//	Search allowed branch name.
	if( array_search($branch, $_config['branch']) !== false ){
		return true;
	}

	//	...
	echo "\n";
	echo "PUSH IS BLOCKED\n";
	echo "Remote: {$remote}\n";
	echo "Branch: {$branch}\n";
	echo "URL: {$url}\n";
	echo "\n";

	//	...
	return false;
}
