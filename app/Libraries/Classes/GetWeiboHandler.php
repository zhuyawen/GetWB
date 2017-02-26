<?php

namespace App\Libraries\Classes; 

use Storage;

/**
 * 获得微博数据基础接口封装
 * @author daweilang
 *
 */


abstract class GetWeiboHandler
{
	
	const VERSION = '1.0.1';
	
	/**
	 * 该条微博id
	 */
	public $mid;

	/**
	 * 该条微博用户id
	 */
	public $uid;
	
	/**
	 * job执行延时
	 */
	public $delay = 5;
	
	/**
	 * 该条微博使用的model
	 * @var object
	 */
	protected $model = 'Weibo';

	/**
	 * 需要获取的该页面地址
	 * @var object
	 */
	protected $thisUrl;
	
	
	/**
	 * 设置抓取任务时抓取的页号
	 */
	protected $getPage;
	
	
	public function __construct()
	{
		//获得全局延时时间设置
		if(config('queue.delay')){
			$this->delay = config('queue.delay');
		}
	}
	
	
	/**
	 * 设置获得信息的任务
	 * @param string $jobName
	 */
	public function setJob($page='1', $jobName=''){}
	
	
	/**
	 * 获得页面
	 * @param string $page 页面的页码
	 */
	public function getHtml($page){}
	
	
	/**
	 * 分析页面内容
	 * @param unknown $html
	 * @param string $file
	 */
	public function explainPage($html, $file =''){}
	
	
	
	/**
	 * 封装queue执行逻辑
	 * @param unknown $className
	 * @param unknown $jobName
	 */
	protected function setQueueClass($className, $classJob, $jobName)
	{
		$class = "\App\Jobs\\$className";
		if(empty($jobName)){
			$job = (new $class($classJob))->delay($this->delay);
		}
		else{
			$job = (new $class($classJob))->onQueue($jobName)->delay($this->delay);
		}
		dispatch($job);
	}
	
	
	/**
	 * 获得微博接口返回的数组 
	 * 返回数组只decode不做处理
	 * @param unknown $content
	 * @param unknown $errorFile
	 * @throws \Exception
	 * @return mixed
	 */
	protected function getHtmlArray($content, $errorFile)
	{
		$array = json_decode($content, true);
		if(!is_array($array) || $array['code'] !== '100000'){
			Storage::put($errorFile, $content);
			throw new \Exception("无法获取接口，请检查获取结果");
		}
		return $array;
	}
}