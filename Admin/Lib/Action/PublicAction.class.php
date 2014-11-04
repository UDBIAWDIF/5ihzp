<?php

class PublicAction extends Action {
	/**
	 * 生成图像验证码
	 * @access public
	 * @return string
	 */
	public function verify(){
		import("ORG.Util.Image");
		Image::buildImageVerify();
	}

	/**
	 * 登陆验证
	 * @access public
	 * @return void
	 */
	public function checkLogin() {
		import ('ORG.Util.Session');
		$Admin=D('Admin');
		$username=trim($_POST['username']);
		$password=trim($_POST['password']);
		$verify=trim($_POST['verify']);

		if($username==''){
			$this->error('用户名不能为空!!!');
		}elseif($password==''){
			$this->error('密码不能为空!!!');
		}elseif(C('ADMIN_LOGIN_VERIFY') && md5($verify)!=Session::get('verify')){
			$this->error('验证码错误!!!');
		}
		$map=array();
		$map['adm_name']=$username;
		$vo = $Admin->where($map)->find();
		if(false===$vo){
			$this->error('用户不存在!!!');
		}else{
			if($vo['adm_password'] != md5($password)) $this->error('密码错误!!!');

			import ('@.ORG.Security' );
			Security::authenticate($vo);
			$defaultUrl = C('INDEX_URL');
			if(empty($defaultUrl)) {
				$defaultUrl = 'Index';
			}
			$this->redirect($defaultUrl . '/index');
		}
	}

	/**
	 * 登出
	 * @access public
	 * @return void
	 */
	public function logout() {
		import('@.ORG.Security' );
		Security::destoryAccess();
		$this->assign('jumpUrl', U('Public/login'));
		$this->success('登出成功，马上跳转');
	}

	public function clearCache() {
		//清空Runtime目录
		//清空S缓存
		import('ORG.Io.Dir');
		$dir = new Dir(RUNTIME_PATH);
		$list = $dir->toArray();
		foreach($list as $d){
			if($d['pathname']==DATA_PATH){
				continue;
			}
			$dir->del($d['pathname']);
		}

		$Cache = Cache::getInstance(C('DATA_CACHE_TYPE'));
		$Cache->clear();
		$this->success('缓存清除成功!');
	}//end clearCache()


	/**
	 * 执行清除缓存前，先将pageview更新到数据库
	 */
	public function _before_clearCache(){
		$this->updateAppPageView();
		$this->updateArtPageView();
		$this->updateDownloads();
	}

	// 删除一定时间之前所有上传的文件,以免服务器被暴硬盘
	// 复用此方法，让它可以删除其它目录下的文件，但方法名就不改了
	public function clearUploadFile($p_path = null, $echoSuccess = true) {
		clearstatcache();
		$uploadPath = trim($p_path);
		if(empty($uploadPath)) {
			$uploadPath = BASE_PATH . C('FILE_UP_PATH');
		}
		$clearTime = $_SERVER['REQUEST_TIME'] - C('UPLOAD_CACHE_TIME');
		$list = listAllFile($uploadPath, true);
		foreach($list as $d) {
			if(filectime($d) <= $clearTime){
				$this->_destroyFile($d);
			}
		}

		$echoSuccess && $this->success('零时文件清除成功!');
	}//end clearUploadFile()

	/**
	 * 更新文章页面浏览量
	 */
	public function updateArtPageView(){
		$article_model =M('Art');
		$article_list = $article_model->field('art_id')->select();

		foreach($article_list as $val){
			$id = $val['art_id'];
			$cache_plus = 'ArticleAction_page_view_plus_0_'.$id;
			$pv = S($cache_plus);
			if($pv){
				$article_model->where('art_id='.$id)->setInc('art_pageview',$pv);
			}
		}
	}

	/**
	 * 更新应用页面浏览量
	 */
	public function updateAppPageView(){
		$app_model =M('App');
		$app_list = $app_model->field('app_id')->select();

		foreach($app_list as $val){
			$id = $val['app_id'];
			$cache_plus = 'AppAction_page_view_plus_1_'.$id;
			$pv = S($cache_plus);
			if($pv){
				$app_model->where('app_id='.$id)->setInc('app_pageview',$pv);
			}
		}
	}

	public function updateDownloads(){
		$app_list = M('App')->field('app_id')->select();
		foreach($app_list as $app){
			$id = $app['app_id'];
			$downloads = S('appdownloads'.$id);
			if($downloads){
				M('App')->where('app_id='.$id)->setInc('app_downloads',$downloads);
				S('appdownloads'.$id, NULL);
			}
		}
	}

	public function getCache($p_key){
		static $cache_model;
		if(!$cache_model) $cache_model = Cache::getInstance(C('DATA_CACHE_TYPE'));
		return $cache_model->get($p_key);
	}

	private function _destroyFile($p_file) {
		@unlink($p_file);
	}

}

?>