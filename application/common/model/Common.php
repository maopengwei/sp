<?php
namespace app\common\model;

use think\model;
use think\model\concern\SoftDelete;

/**
 * 基类model
 */
class Common extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	public function __construct() {
		parent::__construct();
	}
}
