<?php
/** op-unit-cd:/CD.class.php
 *
 * @created    2023-02-05
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
use Exception;
use OP\IF_UNIT;
use OP\OP_CORE;
use OP\OP_CI;

/** CD
 *
 * @created    2023-02-05
 * @version    1.0
 * @package    op-unit-empty
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
class CD implements IF_UNIT
{
	/** use
	 *
	 */
	use OP_CORE, OP_CI;

	/** Automaticall
	 *
	 * @created    2023-02-05
	 */
	static function Auto()
	{
		//	Init
		$branches = [];

		//	...
		$remote = OP()->Request('remote');
		$branch = OP()->Request('branch');
		$force  = OP()->Request('force') ? true: false;

		//	Get the specified remote.
		if( $remote ){
			if( $remote === '\*' ){
				$remotes = self::Git()->Remote()->List();
			}else{
				$remotes[] = $remote;
			}
		}else{
			$remotes[] = 'origin';
		}

		//	Get the specified branch.
		if( $branch ){
			if( $branch === '\*' ){
				//	Generate branches by instanced branch names.
				$branches = self::Git()->Branch()->List(); // Branches();
			}else{
				//	Generate branches by specified branch name.
				$branches[] = $branch;
			}
		}else{
			//	Generate branches by current branch name.
			$branches[] = self::Git()->CurrentBranch();
		}

		//	Execute each remote name.
		foreach( $remotes as $remote ){
			//	Execute each branch name.
			foreach( $branches as $branch_name ){
				//	...
				$commit_id_file   = self::CI()->GenerateFilename($branch_name);
				$commit_id_saved  = file_get_contents($commit_id_file);
				$commit_id_branch = self::Git()->CommitID($branch_name);

				//	...
				if( OP()->Request('debug') and ($commit_id_saved !== $commit_id_branch) ){
					D($remote, $branch_name, $commit_id_file, $commit_id_saved, $commit_id_branch);
				}

				//	...
				if( $commit_id_saved !== $commit_id_branch ){
					throw new Exception("Does not match commit id. ({$commit_id_file}={$commit_id_saved}, {$branch_name}={$commit_id_branch})");
				}

				//	...
				if( $result = self::Git()->Push($remote, $branch_name, $force) ){
					echo $result . "\n";

					//	...
					if( strpos($result, 'error: failed to push some refs to') ){
						throw new Exception("git push was failed.");
					}
				}
			}
		}
	}

	/** Init
	 *
	 * @created    2023-02-05
	 */
	static function Init()
	{

	}

	/** CI
	 *
	 * @created    2023-02-17
	 * @return     CI
	 */
	static function CI()
	{
		return OP()->Unit('CI');
	}

	/** Git
	 *
	 * @created    2023-02-05
	 * @return     Git
	 */
	static function Git()
	{
		return OP()->Unit('Git');
	}
}
