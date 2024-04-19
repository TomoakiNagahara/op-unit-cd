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

			//	...
			$meta_path = OP()->MetaPath($path);

			//	...
			if( file_exists('ci.sh') or file_exists('.ci.sh') ){
				//	OK
			}else{
				continue;
			}

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
		}
	}
}
