<?php
/** op-unit-cd:/CD_2024.trait.php
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

/** use
 *
 */

/** include
 *
 */
require_once(__DIR__.'/function/Display.php');
require_once(__DIR__.'/function/PathList.php');

/** CD_2024
 *
 * @created    2024-04-12
 * @version    1.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
trait CD_2024
{
	/**	Automatically push all submodules individually.
	 *
	 */
	static function Auto()
	{
		try{
			self::CheckGitCommitId();
			self::PushGitRepository();
		}catch( \Throwable $e ){
			OP()->Notice($e);
		}
	}

	/** Check Git Commit Id
	 *
	 */
	static function CheckGitCommitId()
	{
		//	...
		foreach( PathList() as $path ){

			//	...
			if(!is_dir($path) ){
				continue;
			}

			//	...
			chdir($path);

			/*
			//	...
			$meta_path = OP()->MetaPath($path);
			*/

			//	...
			if( file_exists('ci.sh') or file_exists('.ci.sh') ){
				//	OK
			}else{
				continue;
			}

			//	...
			if( self::_CheckGitCommitId() ){
				return;
			}

			/*
			//	...
			$branch = OP()->Request('branch') ?? self::Git()->Branch()->Current();

			//	Get saved commit ID from file.
			$file_name = self::CI()->GenerateFilename($branch);
			if(!file_exists($file_name) ){
				throw new \Exception("Does not found this file. ($meta_path, $file_name)");
			}
			$commit_id_saved = file_get_contents($file_name);

			//	Checking correct commit ID.
			$commit_id = self::Git()->CommitID($branch);
			if( $commit_id_saved !== $commit_id ){
				throw new \Exception("Does not match commit id. ({$meta_path}, {$file_name}={$commit_id_saved}, {$branch}={$commit_id})");
			}
			*/
		}
	}

	/** Git push by .gitmodules file.
	 *
	 */
	static function PushGitRepository()
	{
		//	...
		require_once(__DIR__.'/function/isCanPushToGithub.php');

		//	...
		foreach( PathList() as $path ){

			//	...
			$io = self::_PushGitRepository($path);

			//	...
			if( $io === false ){
				exit(__LINE__);
			}
		}
	}

	/** Execute git push
	 *
	 * @created    2025-07-07
	 * @param      string     $path
	 * @return     bool       $io
	 */
	static private function _PushGitRepository( string $path ) : bool
	{
		//	Check if directory.
		if(!is_dir($path) ){
			//	Not directory.
			return false;
		}

		//	...
		chdir($path);

		//	...
		if( file_exists('ci.sh') or file_exists('.ci.sh') ){
			//	OK
		}else{
			OP()->Error('`ci.sh` was not found: ' . OP()->Path(getcwd()));
			return false;
		}

		//	...
		$remote = OP()->Request('remote') ?? 'origin';
		$branch = OP()->Request('branch') ?? self::Git()->Branch()->Current();
		$force  = OP()->Request('force' ) ?  true: false;
		$force  = false;

		//	...
		if(!isCanPushToGithub($remote, $branch) ){
			return false;
		}

		//	...
		$result = '';
		if( $io = self::Git()->Push($remote, $branch, $force, $result) ){
			//	Success
		}else{
			//	Failed
			if( strpos($result, "[rejected]        {$branch} -> {$branch} (non-fast-forward)") ){
				//	rejected
				$path   = OP()->MetaPath($path);
				$result = "{$path} [rejected] {$branch} -> {$branch} (non-fast-forward)";
			}
		}

		//	...
		echo $result ? "\n{$result}\n": null;

		//	...
		return $io;
	} // For git diff solution.

	/** CI
	 *
	 * @created    2023-02-17
	 * @return    \OP\UNIT\CI
	 */
	static function CI()
	{
		return OP()->Unit('CI');
	}
}
