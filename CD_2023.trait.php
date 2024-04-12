<?php
/** op-unit-cd:/CD_2023.trait.php
 *
 * @created    2023-02-05
 * @moved      2024-04-12  CD.class.php --> CD_2023.trait.php
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

/** CD
 *
 * @created    2023-02-05
 * @moved      2024-04-12  CD --> CD_2023
 * @version    1.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright  Tomoaki Nagahara All right reserved.
 */
trait CD_2023
{
	/** Automatically
	 *
	 * @created    2023-02-05
	 */
	static function Auto()
	{
		//	...
		$git_root = OP()->MetaPath('git:/');

		//	core
		chdir( $git_root.'asset/core/' );
		self::Single();

		//	...
		$list = [
			'unit',
			'layout',
			'module',
			'webpack',
		];

		//	...
		foreach( $list as $dir ){
			//	...
			foreach( glob("{$git_root}/asset/{$dir}/*") as $path ){
				//	...
				chdir($path);

				//	...
				self::Single();

				//	...
				if( $notice = OP()->Notice()->Pop() ){
					self::Display($notice['message']);
				}
			}
		}

		//	main
		chdir( $git_root );
		self::Single();
	}

	/** Single
	 *
	 * @created    2024-04-02
	 */
	static private function Single()
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
			$branches[] = self::Git()->Branch()->Current();
		}

		//	Save current branch name.
		$current_branch_name = self::Git()->Branch()->Current();

		//	Execute each remote name.
		foreach( $remotes as $remote ){

			//	Execute each branch name.
			foreach( $branches as $branch_name ){

				//	...
				$commit_id_file   = self::CI()->GenerateFilename($branch_name);
				$commit_id_branch = self::Git()->CommitID($branch_name);

				//	...
				if(!file_exists($commit_id_file) ){
					OP()->Notice("Does not found this file. ($commit_id_file)");
					continue;
				}

				//	...
				$commit_id_saved  = file_get_contents($commit_id_file);

				//	...
				if(!self::Git()->Switch($branch_name) ){
					OP()->Notice("git switch {$branch_name} was failed.");
					continue;
				}

				//	...
				if( $branch_name !== $current_branch = self::Git()->Branch()->Current() ){
					OP()->Notice("Does not match branch name. (specify={$branch_name}, current={$current_branch})");
					continue;
				}

				//	...
				if( OP()->Request('debug') and ($commit_id_saved !== $commit_id_branch) ){
                    $temp = [
                        'PHP'        => PHP_VERSION,
                        'current'    => getcwd(),
                        'remote'     => $remote,
                        'branch'     => $branch_name,
                        'file path'  => $commit_id_file,
                        'commit id saved'    => $commit_id_saved,
                        'commit id current'  => $commit_id_branch,
                    ];
					D($temp);
				}

				//	...
				if( $commit_id_saved !== $commit_id_branch ){
					OP()->Notice("Does not match commit id. ({$commit_id_file}={$commit_id_saved}, {$branch_name}={$commit_id_branch})");
					continue;
				}

				//	...
				if( $result = self::Git()->Push($remote, $branch_name, $force) ){
					echo $result . "\n";

					//	...
					if( strpos($result, 'error: failed to push some refs to') ){
						OP()->Notice("git push was failed.");
						continue;
					}
				}
			}
		}

		//	...
		self::Git()->Switch($current_branch_name);
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
	 * @return    \OP\UNIT\CI
	 */
	static function CI()
	{
		return OP()->Unit('CI');
	}

	/** Git
	 *
	 * @created    2023-02-05
	 * @return    \OP\UNIT\Git
	 */
	static function Git()
	{
		return OP()->Unit('Git');
	}

	/** Display
	 *
	 */
	static function Display(string $message)
	{
		//	...
		static $_display;

		//	...
		if( empty($_display) ){
			$_display = OP()->Request('display');
		}

		//	...
		if( $_display ){
			$meta_path = OP()->MetaPath( getcwd() );
			echo "{$meta_path} - {$message}\n";
		}
	}
}
