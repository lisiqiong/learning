<?php
/*
 * @desc 计划任务服务
 * @author lisiqiong
*/
class ScheduleModel{
	private $model;
	private $schedule		= array();
	private $scheduleList 	= array();
	private $redisKey = 'scheduleList';//redis保存表里面数据的key值
	/**
	 * @desc 执行计划任务列表
	 * */
	public function Run(){
		//锁定自动执行 修正一下
		$lockfile = $this->getLogPath() . '/schedule.lock';
		//锁定未过期 - 返回
		if( file_exists($lockfile) && ( (filemtime($lockfile))+60 > $_SERVER['REQUEST_TIME'] )){
			return;
		} else {
			//重新生成锁文件
			touch($lockfile);
		}
		//执行计划任务
		$this->runScheduleList($this->getScheduleList());
		//解除锁定
		unlink($lockfile);
		return;
	}
	
	/**
	 * @desc 执行计划任务列表
	 * ***/
	public function runScheduleList($scheduleList) {
		foreach( $scheduleList as $key => $schedule ) {
			$date = $this->calculateNextRunTime($schedule);
			if( $date != false && $date <= time()) {
				$this->runSchedule($schedule);
			}else {
				continue;
			}
		}
		return true;
	}	
	
	/**
	 * @desc 运行计划任务
	 * **/
	public function runSchedule($schedule) {
		// 获取后台配置的计划任务
		$checkScheduleList = $this->getScheduleList();
		$checkScheduleList = $this->getSubByKey($checkScheduleList, 'task_to_run');
		if (!in_array($schedule['task_to_run'], $checkScheduleList)) {
			$str_log = "schedule_id = {$schedule['id']} 的任务不合法。";
			$this->_log($str_log);
			return false;
		}
		//解析task类型, 并运行task
		$task_to_run = explode('/',$schedule['task_to_run']);
		$modelStr = "D('{$task_to_run[0]}')->{$task_to_run[1]}();";
		/**-----这里开始处理任务模型------**/
		eval($modelStr);
		/**-----这里结束处理任务模型下面开始更新数据表------**/
		if(strtoupper($schedule['schedule_type']) == 'ONCE') {
			//ONCE类型的计划任务，将end_datetime设置为当前时间
			if(empty($schedule['last_run_time'])){
				$schedule['last_run_time'] = date('Y-m-d H:i:s');
			}else{
				return false;
			}
		}else {
			//非ONCE类型的计划任务， 防止由程序执行导致的启动时间的漂移
			if(in_array($schedule['schedule_type'], array('MINUTE', 'HOURLY'))) {
				//将last_run_time设置为当前时间（秒数设为0）
				$schedule['last_run_time'] = date('Y-m-d H:i:s',$this->setSecondToZero());
			}else {
				//将last_run_time设置为当前日期+预定时间
				$now_date = date('Y-m-d');
				$fixed_time = date('H:i:s', strtotime($schedule['start_datetime']));
				$schedule['last_run_time'] = $now_date . ' ' . $fixed_time;
			}
		}
		$this->saveSchedule($schedule);
		$str_log = "schedule_id = {$schedule['id']} 的任务已运行。";
		$this->_log($str_log);
	}
	
	/**
	 * @desc 计算定时任务下次执行时间
	 * @return 'Y-m-d H:i:s'
	 * ***/
	public function  calculateNextRunTime($schedule){
		//已过期
		if( (strtotime($schedule['end_datetime'])>0) && (strtotime($schedule['end_datetime']) < strtotime(date('Y-m-d H:i:s'))) ) {
			return false;
		}
		//还未启动
		if( strtotime($schedule['start_datetime']) > strtotime(date('Y-m-d H:i:s')) ) {
			return false;
		}
		//已执行
		if( strtotime($schedule['last_run_time']) > strtotime(date('Y-m-d H:i:s')) ) {
			return false;
		}
		$modifier = empty($schedule['modifier']) ? 1 : $schedule['modifier'];
		if( !empty($schedule['last_run_time']) && (strtotime($schedule['last_run_time']) > strtotime($schedule['start_datetime']))) {
			$date = is_string($schedule['last_run_time']) ? strtotime($schedule['last_run_time']) : $schedule['last_run_time'];
		}else {//如果没有执行过则默认为开始时间执行
			$date = $this->_getStartDateTime($schedule);
		}
		$type = $schedule['schedule_type'];
		switch($type){
			case 'ONCE':
				if(!empty($schedule['last_run_time'])){
					return false;
				}
				$datetime =  $this->_getStartDateTime($schedule);
				break;
			case 'MINUTE':
				if($modifier>=1 && $modifier<=60){
					$datetime =  mktime(date('H',$date),date('i',$date) + $modifier,date('s',$date),date('m',$date),date('d',$date),date('Y',$date));
				}else{
					$str_log = "schedule_id = {$schedule['id']} 的任务不合法，MINUTE类型，执行的频率必须为1-60的数字。";
					$this->_log($str_log);
					return false;
				}
				break;
			case 'HOURLY':
				if($modifier>=1 && $modifier<=24){
					$datetime = mktime(date('H',$date) + $modifier,date('i',$date),date('s',$date),date('m',$date),date('d',$date),date('Y',$date));
				}else{
					$str_log = "schedule_id = {$schedule['id']} 的任务不合法，HOURLY类型执行的频率必须为1-24的数字。";
					$this->_log($str_log);
					return false;
				}
				break;
			case 'DAILY':
				if($modifier>=1 && $modifier<=31){
					$datetime = mktime(date('H',$date),date('i',$date),date('s',$date),date('m',$date),date('d',$date) + $modifier,date('Y',$date));
				}else{
					$str_log = "schedule_id = {$schedule['id']} 的任务不合法，DAILY类型执行的频率必须为1-31的数字。";
					$this->_log($str_log);
					return false;
				}				
				break;
			case 'MONTHLY':
				if($modifier>=1 && $modifier<=12){
					$datetime = mktime(date('H',$date),date('i',$date),date('s',$date),date('m',$date)+$modifier,date('d',$date),date('Y',$date));
				}else{
					$str_log = "schedule_id = {$schedule['id']} 的任务不合法，MONTHLY类型执行的频率必须为1-12的数字。";
					$this->_log($str_log);
					return false;
				}
				break;
			default:
				return false;
		}
		return $datetime;
	}
	
	/*
	 *@desc 设置定时任务文件目录
	*/
	public function getLogPath(){
		$logPath = './Public/schedule_log';
		if(!is_dir($logPath))
			@mkdir($logPath,0777);
		return $logPath;
	}
	
	/**
	 * @desc 检测定时任务写入执行情况
	 * **/
	protected function _log($str){
		$filename = $this->getLogPath() . '/schedule_' . date('Y-m-d') . '.log';
		$str = '[' . date('Y-m-d H:i:s') . '] ' . $str;
		$str .= "\r\n";
		$handle = fopen($filename, 'a');
		fwrite($handle, $str);
		fclose($handle);
	}
	
	//获取开始时间
	//@return timestamp
	protected function _getStartDateTime($schedule) {
		if( !empty($schedule['start_datetime']) ) {
			return strtotime($schedule['start_datetime']);
		}else {
			return false;
		}
	}
	
	/***
	 *@desc 将给定时间的秒数置为0; 参数为空时，使用当前时间
	 * ***/
	protected function setSecondToZero($date_time = NULL) {
		if(empty($date_time)) {
			$date_time = date('Y-m-d H:i:s');
		}
		$date_time = is_string($date_time) ? strtotime($date_time) : $date_time;
		return mktime(date('H', $date_time),date('i', $date_time),0,date('m', $date_time),date('d', $date_time),date('Y', $date_time));
	}
	
	/**
	 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
	 * @param $pArray 一个二维数组
	 * @param $pKey 数组的键的名称
	 * @return 返回新的一维数组
	 */
	public function getSubByKey($pArray, $pKey="", $pCondition=""){
		$result = array();
		if(is_array($pArray)){
			foreach($pArray as $temp_array){
				if(is_object($temp_array)){
					$temp_array = (array) $temp_array;
				}
				if((""!=$pCondition && $temp_array[$pCondition[0]]==$pCondition[1]) || ""==$pCondition) {
					$result[] = (""==$pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
				}
			}
			return $result;
		}else{
			return false;
		}
	}
	
	/***
	 * @desc 修改计划任务本次执行时间
	 * **/
	public function saveSchedule($schedule){
		if(empty($schedule)){
			return array();
		}
		$data = array('id'=>$schedule['id'],'last_run_time'=>$schedule['last_run_time'],);
		return $this->save($data);
	}
	
	/**
	 * @desc 获取定时任务列表信息
	 * **/
	public function getScheduleList(){
		$where['status'] = 1;//开启
		$infoArr = $this->where($where)->select();
		return $infoArr;
		/*$redis = redis();
		$redInfo = $redis->get($this->redisKey);
		if($redInfo){
			return unserialize($redInfo);
		}else{
			$where['status'] = 0;
			$infoArr = $this->where($where)->select();
			$info = serialize($infoArr);
			$redis->set($this->redisKey,$info,300);
			return $infoArr;
		}*/
	}
}
?>
