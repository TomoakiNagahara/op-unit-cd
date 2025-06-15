<?php
/** op-unit-cd:/ci/CD.php
 *
 * @created    2023-01-30
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
namespace OP;

/* @var $ci \OP\UNIT\CI\CI_Config */
$ci = OP::Unit('CI')::Config();

//	Auto
$args   = null;
$result = null;
$ci->Set('Auto', $result, $args);

//	Git
$args   =  null;
$result = 'OP\UNIT\Git';
$ci->Set('Git', $result, $args);

//	...
return $ci->Get();
