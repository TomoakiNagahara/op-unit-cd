<?php
/**	op-unit-cd:/FTP.class.php
 *
 * @created    2026-04-19
 * @license    Apache-2.0
 * @package    op-unit-cd
 * @author     Tomoaki Nagahara
 * @copyright  Tomoaki Nagahara
 */

/**	Declare strict type
 *
 */
declare(strict_types=1);

/**	Namespace
 *
 */
namespace OP\UNIT\CD;

/**	Use
 *
 */
use OP\OP_CORE;
use OP\OP_CI;
use OP\IF_CD;

/**	FTP
 *
 * @created    2026-04-19
 */
class FTP implements IF_CD
{
	/**	trait
	 *
	 */
	use OP_CORE, OP_CI;

	/**	Automatically
	 *
	 * @created    2026-04-19
	 */
	static function Auto( array $config )
	{
		//	...
		$host     = $config['host'];
		$user     = $config['user'];
		$password = $config['password'];
		$passive  = $config['passive'];
		$local    = $config['local'];
		$remote   = $config['remote'];

		//	Check password exists.
		if(!$password ){
			$password = self::GetPassword();
		}

		//	...
		$host     = escapeshellarg($host);
		$local    = escapeshellarg($local);
		$remote   = escapeshellarg($remote);
		$login    = escapeshellarg("{$user},{$password}");

		//	...
		$exclude = '--exclude-glob=.* --exclude-glob=_* --exclude=.git';

		//	...
		$commands[] = $passive ? 'set ftp:passive-mode on': 'set ftp:passive-mode off';
		$commands[] = "mirror -R --parallel=4 --dereference {$exclude} {$local} {$remote}";
		$commands[] = "quit";
		$commands = implode('; ', $commands);
		$comand = "lftp -u {$login} {$host} -e \"{$commands}\"";
	//	D($comand);
		passthru($comand);
	}

	/**	Get password from user input.
	 *
	 * @created    2026-04-19
	 * @return     string
	 */
	static function GetPassword() : string
	{
		//	Output message.
		fwrite(STDOUT, 'FTP PASSWORD: ');

		//	Input echo is off.
		system('stty -echo');

		//	Get password.
		$password = trim(fgets(STDIN));

		//	Input echo is on.
		system('stty echo');
		fwrite(STDOUT, PHP_EOL);

		//	...
		return $password;
	}
}
