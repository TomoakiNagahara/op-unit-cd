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

	/** Check if the current branch is inspected.
	 *
	 * @created    2024-04-21
	 * @return     bool
	 */
	static private function _CheckGitCommitId() : bool
	{
		//	...
		if(!$meta_path = OP()->Path( getcwd() ) ){
			OP()->Error("OP()->Path() is return false.");
			return false;
		}

		//	...
		if(!$branch = OP()->Request('branch') ?? self::Git()->Branch()->Current() ){
			OP()->Error("Branch name is empty.");
			return false;
		}

		//	Get saved commit ID from file.
		$file_name = self::CI()->GenerateFilename($branch);
		if(!file_exists($file_name) ){
			OP()->Error("Does not found this file. ($meta_path, $file_name)");
			return false;
		}

		//	...
		if(!$commit_id_saved = file_get_contents($file_name) ){
			OP()->Error("Does not saved commit id. ($file_name)");
			return false;
		}

		//	Checking correct commit ID.
		if(!$commit_id = self::Git()->CommitID($branch) ){
			OP()->Error("Empty commit id. ($branch)");
			return false;
		}

		//	Why does it include a line break?
		$commit_id_saved = trim($commit_id_saved);

		//	...
		if( $commit_id_saved !== $commit_id ){
			OP()->Error("Does not match commit id. ({$meta_path}, {$file_name}={$commit_id_saved}, {$branch}={$commit_id})");
			return false;
		}

		//	...
		return true;
	}
}
