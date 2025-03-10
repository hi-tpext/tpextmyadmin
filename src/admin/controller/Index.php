<?php

namespace tpext\myadmin\admin\controller;

use think\facade\Db;
use tpext\think\App;
use think\Controller;
use tpext\common\Tool;
use think\facade\Cache;
use tpext\common\ExtLoader;
use tpext\myadmin\common\Module;
use think\captcha\facade\Captcha;
use tpext\builder\common\Builder;
use tpext\myadmin\admin\model\AdminMenu;
use tpext\myadmin\admin\model\AdminUser;
use tpext\myadmin\admin\model\AdminRoleMenu;
use tpext\myadmin\admin\model\AdminPermission;
use tpext\myadmin\admin\model\AdminOperationLog;
use tpext\myadmin\admin\model\AdminRolePermission;

/**
 * Undocumented class
 * @title 首页
 */
class Index extends Controller
{
    /**
     * Undocumented variable
     *
     * @var AdminUser
     */
    protected $dataModel;
    /**
     * Undocumented variable
     *
     * @var AdminMenu
     */
    protected $menuModel;
    /**
     * AdminRoleMenu
     *
     * @var AdminRolePermission
     */
    protected $roleMenuModel;
    /**
     * Undocumented variable
     *
     * @var AdminRolePermission
     */
    protected $rolePerModel;
    /**
     * Undocumented variable
     *
     * @var AdminPermission
     */
    protected $perModel;

    protected function initialize()
    {
        $this->dataModel = new AdminUser;
        $this->menuModel = new AdminMenu;
        $this->roleMenuModel = new AdminRoleMenu;
        $this->rolePerModel = new AdminRolePermission;
        $this->perModel = new AdminPermission;
    }

    public function index()
    {
        $admin_user = session('admin_user');
        $menus = [];
        if ($admin_user['role_id'] == 1) {
            $list = $this->menuModel->where(['enable' => 1])->order('parent_id,sort')->select();
            if (count($list) == 0 && $admin_user['id'] == 1) {
                $menus = [
                    [
                        'id' => 1,
                        'name' => '首页',
                        'url' => url('welcome'),
                        'pid' => 0,
                        'icon' => 'mdi mdi-home',
                        'is_out' => 0,
                        'is_home' => 1,
                    ],
                    [
                        'id' => 2,
                        'name' => '系统管理',
                        'url' => '#',
                        'pid' => 0,
                        'icon' => 'mdi mdi-settings',
                        'is_out' => 0,
                        'is_home' => 0,
                    ],
                    [
                        'id' => 3,
                        'name' => '菜单管理',
                        'url' => url('menu/index'),
                        'pid' => 2,
                        'icon' => 'mdi mdi-arrange-send-to-back',
                        'is_out' => 0,
                        'is_home' => 0,
                    ],
                    [
                        'id' => 4,
                        'name' => '权限设置',
                        'url' => url('permission/index'),
                        'pid' => 2,
                        'icon' => 'mdi mdi-account-key',
                        'is_out' => 0,
                        'is_home' => 0,
                    ], [
                        'id' => 5,
                        'name' => '管理员',
                        'url' => url('admin/index'),
                        'pid' => 2,
                        'icon' => 'mdi mdi-account-card-details',
                        'is_out' => 0,
                        'is_home' => 0,
                    ], [
                        'id' => 6,
                        'name' => '角色管理',
                        'url' => url('role/index'),
                        'pid' => 2,
                        'icon' => 'mdi mdi-account-multiple',
                        'is_out' => 0,
                        'is_home' => 0,
                    ], [
                        'id' => 7,
                        'name' => '扩展管理',
                        'url' => url('extension/index'),
                        'pid' => 2,
                        'icon' => 'mdi mdi-blur',
                        'is_out' => 0,
                        'is_home' => 0,
                    ],
                ];
            } else {
                foreach ($list as $li) {
                    $menus[] = [
                        'id' => $li['id'],
                        'name' => $li['title'],
                        'url' => $li['url'],
                        'pid' => $li['parent_id'],
                        'icon' => 'mdi ' . $li['icon'],
                        'is_out' => 0,
                        'is_home' => $li['id'] == 1 ? 1 : 0,
                    ];
                }
            }
        } else {
            $menus = $this->menuModel->buildMenus($admin_user);
        }

        $config = Module::getInstance()->getConfig();

        $this->assign('admin_user', $admin_user);
        $this->assign('menus', json_encode($menus));
        $this->assign('dashbord', count($menus) ? $menus[0] : ['id' => 1, 'url' => url('welcome'), 'name' => '首页', 'pid' => 0, 'icon' => 'mdi mdi-home', 'is_out' => 0, 'is_home' => 1]);
        $this->assign('index_top_menu', $config['index_top_menu'] ?? 1);

        $template = 'index';
        if (!empty($config['index_page_style']) && $config['index_page_style'] != 1) { //下拉选择的其他模板
            $template = $config['index_page_style'];

            $template = str_replace('__WWW__', App::getRootPath(), $template);

            if (!is_file($template)) { //其他模板不存在，回到默认
                $template = 'index';
            }
        }

        $config = [];
        if (preg_match('/(.+?[\/\\\]view[\/\\\]).+?/', $template, $mch)) {
            $config['view_path'] = $mch[1];
        }
        
        return $this->fetch($template, [], $config);
    }

    public function denied()
    {
        return '<span style="color:#333;font-size:12px;">无权限访问！</span>';
    }

    /**
     * Undocumented function
     *
     * @title 欢迎页面
     * @return mixed
     */
    public function welcome()
    {
        $sysInfo['os'] = PHP_OS;
        $sysInfo['zlib'] = function_exists('gzclose') ? '是' : '否';

        $sysInfo['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sysInfo['curl'] = function_exists('curl_init') ? '是' : '否';
        $sysInfo['web_server'] = request()->server('SERVER_SOFTWARE');
        $sysInfo['user_agent'] = request()->server('HTTP_USER_AGENT');
        $sysInfo['php_version'] = phpversion();
        $sysInfo['ip'] = request()->ip();
        $sysInfo['fileupload'] = @ini_get('upload_max_filesize') ?: '未知';
        $sysInfo['sys_time'] = date('Y-m-d H:i:s', time());
        $sysInfo['max_ex_time'] = @ini_get("max_execution_time") . 's';
        $sysInfo['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sysInfo['domain'] = request()->host();
        $sysInfo['memory_limit'] = ini_get('memory_limit');
        $mysqlinfo = Db::query('select VERSION() as version');
        $sysInfo['mysql_version'] = json_encode($mysqlinfo);
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $sysInfo['gdinfo'] = $gd['GD Version'];
        } else {
            $sysInfo['gdinfo'] = "未知";
        }
        return $this->fetch('', ['sys_info' => $sysInfo]);
    }

    public function logout()
    {
        session('admin_user', null);
        session('admin_id', null);

        $config = Module::getInstance()->getConfig();

        session('admin_last_time', null);

        if (isset($config['login_session_key']) && $config['login_session_key'] == '1') {
            $this->success('注销成功！', '/');
        } else {
            $this->success('注销成功！', url('/admin/index/login'));
        }
    }

    /**
     * Undocumented function
     *
     * @title 修改个人密码
     * @return mixed
     */
    public function changePwd()
    {
        if (request()->isPost()) {
            $data = request()->only([
                'password_old',
                'password_new',
                'password_confirm',
            ], 'post');

            $result = $this->validate($data, [
                'password_old|原密码' => 'require',
                'password_new|新密码' => 'require',
                'password_confirm|确认新密码' => 'require',
            ]);

            if (true !== $result) {

                $this->error($result);
            }

            $this->checkToken();

            $user = $this->dataModel->find(session('admin_id'));

            if (!$this->dataModel->passValidate($user['password'], $user['salt'], $data['password_old'])) {
                $this->error('原密码不正确');
            }

            if ($data['password_new'] != $data['password_confirm']) {
                $this->error('两次输入新密码不匹配');
            }

            if ($data['password_new'] == $data['password_old']) {
                $this->error('新旧密码一样');
            }

            $password = $this->dataModel->passCrypt($data['password_new']);

            $editData['password'] = $password[0];
            $editData['salt'] = $password[1];

            $res = $user->force()->save($editData);

            if ($res) {
                ExtLoader::trigger('admin_change_pwd', $user);

                $user = $this->dataModel->find($user['id']);

                unset($user['password'], $user['salt']);

                session('admin_user', $user->toArray());

                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        } else {
            $builder = Builder::getInstance('个人设置', '修改密码');

            $form = $builder->form();

            $form->password('password_old', '原密码')->required()->help('输入您现在使用的密码');
            $form->password('password_new', '新密码')->required()->help('输入新密码（6～20位）');
            $form->password('password_confirm', '确认新密码')->required()->help('再次输入新密码');

            return $builder->render();
        }
    }

    /**
     * Undocumented function
     *
     * @title 个人资料
     * @return mixed
     */
    public function profile()
    {
        if (request()->isPost() && !input('post.__search__', '0')) {
            return $this->saveProfile();
        } else {
            $builder = Builder::getInstance('个人设置', '资料修改');

            $form = $builder->form(6);
            $form->show('username', '登录帐号')->size(3, 9);
            $form->text('name', '姓名')->required()->beforSymbol('<i class="mdi mdi-rename-box"></i>')->size(3, 9);
            $form->image('avatar', '头像')->default('/assets/lightyearadmin/images/no-avatar.jpg')->size(3, 9)->imageResize(200, 200);
            $form->text('email', '电子邮箱')->beforSymbol('<i class="mdi mdi-email-variant"></i>')->size(3, 9);
            $form->text('phone', '手机号')->beforSymbol('<i class="mdi mdi-cellphone-iphone"></i>')->size(3, 9);
            $form->show('login_time', '登录时间')->size(3, 9);
            $form->show('create_time', '添加时间')->size(3, 9);
            $form->show('update_time', '修改时间')->size(3, 9);

            $form->butonsSizeClass('btn-xs');

            $user = $this->dataModel->find(session('admin_id'));

            $form->fill($user);

            /*******************************/

            $table = $builder->table(6);

            $table->show('id', 'ID');
            $table->show('path', '路径');
            $table->show('ip', 'IP');
            $table->show('create_time', '登录时间');
            $table->getToolbar()
                ->btnRefresh();
            $table->useActionbar(false);
            $table->useCheckbox(false);

            $pagesize = input('__pagesize__/d');

            $pagesize = $pagesize ? $pagesize : 10;

            $page = input('__page__/d', 1);

            $page = $page < 1 ? 1 : $page;

            $count = 0;

            $where['user_id'] = ['=', session('admin_id')];
            $where['path'] = ['like', 'admin/index/login'];

            $sortOrder = input('__sort__', 'id desc');

            $count = AdminOperationLog::where($where)->count();
            $data = AdminOperationLog::where($where)->order($sortOrder)->limit(($page - 1) * $pagesize, $pagesize)->select();

            $table->data($data);
            $table->paginator($count, $pagesize);
            $table->useExport(false);
            $table->sortOrder($sortOrder);

            if (request()->isAjax()) {
                return $table->partial()->render();
            }

            return $builder->render();
        }
    }

    private function saveProfile()
    {
        $data = request()->only([
            'name',
            'avatar',
            'email',
            'phone',
        ], 'post');

        $result = $this->validate($data, [
            'name|姓名' => 'require',
            'email|电子邮箱' => 'email',
            'phone|手机号' => 'mobile',
        ]);

        if (true !== $result) {

            $this->error($result);
        }

        $this->checkToken();

        $res = $this->dataModel->where(['id' => session('admin_id')])->save($data);

        if ($res) {

            $user = $this->dataModel->find(session('admin_id'));

            unset($user['password'], $user['salt']);

            session('admin_user', $user->toArray());

            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    /**
     * Undocumented function
     *
     * @title 清空缓存
     * @return mixed
     */
    public function clearCache()
    {
        if (request()->isPost()) {

            $this->checkToken();

            $types = input('post.types');

            if (empty($types)) {
                $this->error('请选择清除类型');
            }

            if (in_array(1, $types)) {
                Cache::clear();
            }
            if (in_array(2, $types)) {
                Tool::deleteDir(App::getRuntimePath() . 'temp');
            }
            if (in_array(3, $types)) {

                $dirs = ['', 'assets', 'minify', ''];
                
                $minifyDir = App::getPublicPath() . implode(DIRECTORY_SEPARATOR, $dirs);

                Tool::deleteDir($minifyDir);
            }

            $this->success('操作成功！');
        } else {
            $builder = Builder::getInstance('系统设置', '清空缓存');

            $form = $builder->form();

            $form->checkbox('types', '要清除的缓存类型')->options([
                1 => '数据缓存[cache]',
                2 => '模板缓存[temp]',
                3 => '资源压缩[minify]',
            ])->checkallBtn('全部')->inline(false);

            return $builder->render();
        }
    }

    public function login()
    {
        $config = Module::getInstance()->getConfig();

        if (isset($config['login_session_key']) && $config['login_session_key'] == '1') {
            if (!session('?login_session_key')) {
                if (cookie('tpext_myadmin_entry')) {
                    return $this->success('验证中...', cookie('tpext_myadmin_entry'), '', 1);
                }
                header("HTTP/1.1 404 Not Found");
                exit;
            }
        }

        if (request()->isPost()) {
            $data = request()->only([
                'username',
                'password',
                'captcha',
            ], 'post');

            $result = $this->validate($data, [
                'username|登录帐号' => 'require',
                'password|密码' => 'require',
                'captcha|验证码' => 'require',
            ]);

            if (true !== $result) {

                $this->error($result);
            }

            if (!Captcha::check($data['captcha'])) {
                $this->error('验证码错误');
            }

            $user = $this->dataModel->where(['username' => $data['username']])->find();

            if (!$user) {
                $this->error('用户帐号不存');
            }

            if ($user['enable'] == 0) {
                $this->error('帐号已禁用');
            }

            if ($user['errors'] > 10) {

                $errors = $user['errors'] > 300 ? 300 : $user['errors'];

                $try_login = Cache::get('admin_try_login_' . $user['id']);

                if ($try_login) {

                    $time_gone = time() - $try_login;

                    if ($time_gone < $errors) {
                        $this->error('错误次数过多，请' . ($errors - $time_gone) . '秒后再试');
                    }
                }
            }

            if (!$this->dataModel->passValidate($user['password'], $user['salt'], $data['password'])) {

                $this->dataModel->where(['id' => $user['id']])->inc('errors');

                Cache::set('admin_try_login_' . $user['id'], time());

                $this->error('密码错误');
            }

            $this->dataModel->where(['id' => $user['id']])->update(['login_time' => date('Y-m-d H:i:s'), 'errors' => 0]);

            Cache::set('admin_try_login_' . $user['id'], null);
            unset($user['password'], $user['salt']);
            session('admin_user', $user->toArray());
            session('admin_id', $user['id']);
            session('login_session_key', null);

            session('admin_last_time', time());

            AdminOperationLog::create([
                'user_id' => $user['id'],
                'path' => 'admin/index/login',
                'method' => request()->method(),
                'ip' => request()->ip(),
                'data' => json_encode([])
            ]);

            ExtLoader::trigger('admin_login', $user);

            $this->success('登录成功', cookie('after_login_url'));
        } else {

            if (!Module::isInstalled()) {
                Cache::clear();
            }

            $this->assign(['login_in_top' => $config['login_in_top'], 'login_css_file' => $config['login_css_file']]);

            $rootPath = App::getRootPath();

            $template = '';
            if (!empty($config['login_page_view_path']) && file_exists($rootPath . $config['login_page_view_path'])) { //直接填写的模板路径
                $template = $rootPath . $config['login_page_view_path'];
            } else { //下拉选择模板路径
                $template = 'login1';
                if (!empty($config['login_page_style'])) {
                    if (is_numeric($config['login_page_style'])) { // 1,2,3,4 默认样式
                        $template = 'login' . $config['login_page_style'];
                    } else { //其他
                        $template = $config['login_page_style'];

                        $template = str_replace('__WWW__', $rootPath, $template);

                        if (!is_file($template)) { //其他模板不存在，回到默认3
                            $template = 'login3';
                        }
                    }
                }
            }

            $config = [];
            if (preg_match('/(.+?[\/\\\]view[\/\\\]).+?/', $template, $mch)) {
                $config['view_path'] = $mch[1];
            }

            return $this->fetch($template, [], $config);
        }
    }

    public function captcha()
    {
        $config = Module::getInstance()->getConfig();

        if (isset($config['login_session_key']) && $config['login_session_key'] == '1') {
            if (!session('?login_session_key')) {
                header("HTTP/1.1 404 Not Found");
                exit;
            }
        }

        return Captcha::create();
    }

    protected function checkToken()
    {
        $token = session('_csrf_token_');

        if (empty($token) || $token != input('__token__')) {
            $this->error('token错误');
        }
    }
}
