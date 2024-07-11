<?php
namespace ymj666\view\driver;

use RuntimeException;
use Smarty\Exception;
use Smarty\Smarty;
use think\App;
use think\contract\TemplateHandlerInterface;
use think\helper\Str;

/**
 * Smarty 模板引擎驱动
 *
 * @see Smarty
 */
class ThinkSmarty implements TemplateHandlerInterface {

    protected Smarty $template;
    protected App $app;
    // 模板引擎配置
    protected array $config = [
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
        'auto_rule' => 1,
        // 视图目录名
        'view_dir_name' => '',
        // 应用模板路径
        'view_path' => '',
        // 模板文件后缀
        'view_suffix' => 'tpl',
        // 模板文件名分隔符
        'view_depr' => DIRECTORY_SEPARATOR,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 是否开启模板编译缓存，设为false则每次都会重新编译
        'tpl_cache' => true,
        // 模板编译缓存目录
        'tpl_cache_path' => '',
        // 是否开启模板渲染缓存，设置为true则缓存过期前会直接读取渲染结果输出
        'display_cache' => false,
        // 模板渲染缓存目录
        'display_cache_path' => '',
        // 模板渲染缓存有效期，单位：秒。设置为0则永不过期
        'display_cache_time' => 3600,
        // 模板输出替换
        'tpl_replace_string' => [],
    ];

    /**
     * @throws Exception
     */
    public function __construct(App $app, array $config = []) {
        $this->app = $app;
        $this->config = array_merge($this->config, $config);

        if (!$this->config['tpl_cache_path']) {
            $this->config['tpl_cache_path'] = runtime_path('temp');
        }
        if (!$this->config['display_cache_path']) {
            $this->config['display_cache_path'] = runtime_path('display_cache');
        }

        $smarty = new Smarty();
        $smarty->setLeftDelimiter($this->config['tpl_begin']);
        $smarty->setRightDelimiter($this->config['tpl_end']);
        $smarty->setTemplateDir($this->config['view_path']);
        $smarty->setForceCompile(!$this->config['tpl_cache']);
        $smarty->setCompileDir($this->config['tpl_cache_path']);
        $smarty->setCaching($this->config['display_cache'] ? Smarty::CACHING_LIFETIME_SAVED : Smarty::CACHING_OFF);
        $smarty->setCacheDir($this->config['display_cache_path']);
        $smarty->setCacheLifetime($this->config['display_cache_time'] === 0 ? -1 : $this->config['display_cache_time']);
        $this->template = $smarty;

        $this->registerThinkFunctions();
    }

    /**
     * 检测是否存在模板文件
     *
     * @param string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists(string $template): bool {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }

        return is_file($template);
    }

    /**
     * 渲染模板文件
     *
     * @param string $template 模板文件
     * @param array $data 模板变量
     * @return void
     * @throws Exception
     */
    public function fetch(string $template, array $data = []): void {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }

        // 模板不存在 抛出异常
        if (!is_file($template)) {
            throw new RuntimeException('template not exists:' . $template);
        }

        $this->template->assign($data);
        $replace = $this->config['tpl_replace_string'];
        $content = $this->template->fetch($template);
        $content = str_replace(array_keys($replace), array_values($replace), $content);

        echo $content;
    }

    /**
     * 渲染模板内容
     *
     * @param string $content 模板内容
     * @param array $data 模板变量
     * @return void
     * @throws Exception
     */
    public function display(string $content, array $data = []): void {
        $this->template->assign($data);
        $content = $this->template->fetch('eval:' . $content);
        $replace = $this->config['tpl_replace_string'];
        $content = str_replace(array_keys($replace), array_values($replace), $content);

        echo $content;
    }

    /**
     * 自动定位模板文件
     *
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate(string $template): string {
        $request = $this->app->request;

        // 获取视图根目录
        if (str_contains($template, '@')) {
            // 跨应用调用
            [$app, $template] = explode('@', $template);
        }

        if ($this->config['view_path'] && !isset($app)) {
            $path = $this->config['view_path'];
        } else {
            $appName = $app ?? $this->app->http->getName();
            $view = $this->config['view_dir_name'];

            if (is_dir($this->app->getAppPath() . $view)) {
                if (isset($app)) {
                    $path = $this->app->getBasePath() . ($appName ? $appName . DIRECTORY_SEPARATOR : '') . $view . DIRECTORY_SEPARATOR;
                } else {
                    $path = $this->app->getAppPath() . $view . DIRECTORY_SEPARATOR;
                }
            } else {
                $path = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . ($appName ? $appName . DIRECTORY_SEPARATOR : '');
            }
        }

        $depr = $this->config['view_depr'];

        if (!str_starts_with($template, '/')) {
            $template = str_replace(['/', ':'], $depr, $template);
            $controller = $request->controller();

            if (str_contains($controller, '.')) {
                $pos = strrpos($controller, '.');
                $controller = substr($controller, 0, $pos) . '.' . Str::snake(substr($controller, $pos + 1));
            } else {
                $controller = Str::snake($controller);
            }

            if ($controller) {
                if ($template === '') {
                    // 如果模板文件名为空 按照默认规则定位
                    if (2 === $this->config['auto_rule']) {
                        $template = $request->action(true);
                    } elseif (3 === $this->config['auto_rule']) {
                        $template = $request->action();
                    } else {
                        $template = Str::snake($request->action());
                    }

                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                } elseif (!str_contains($template, $depr)) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }

        return $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');
    }

    /**
     * 配置模板引擎
     *
     * @param array $config 参数
     * @return void
     */
    public function config(array $config): void {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取模板引擎配置
     *
     * @param string $name 参数名
     * @return mixed
     */
    public function getConfig(string $name) {
        return $this->config[$name] ?? null;
    }

    /**
     * 注册ThinkPHP的函数到Smarty模板引擎
     *
     * @return void
     * @throws Exception
     */
    private function registerThinkFunctions() {
        $this->template->registerPlugin('modifier', 'cache', 'cache');
        $this->template->registerPlugin('modifier', 'config', 'config');
        $this->template->registerPlugin('modifier', 'cookie', 'cookie');
        $this->template->registerPlugin('modifier', 'env', 'env');
        $this->template->registerPlugin('modifier', 'input', 'input');
        $this->template->registerPlugin('modifier', 'lang', 'lang');
        $this->template->registerPlugin('modifier', 'parse_name', 'parse_name');
        $this->template->registerPlugin('modifier', 'session', 'session');
        $this->template->registerPlugin('modifier', 'token', 'token');
        $this->template->registerPlugin('modifier', 'token_field', 'token_field');
        $this->template->registerPlugin('modifier', 'token_meta', 'token_meta');
        $this->template->registerPlugin('modifier', 'url', 'url');
        $this->template->registerPlugin('modifier', 'app_path', 'app_path');
        $this->template->registerPlugin('modifier', 'base_path', 'base_path');
        $this->template->registerPlugin('modifier', 'config_path', 'config_path');
        $this->template->registerPlugin('modifier', 'public_path', 'public_path');
        $this->template->registerPlugin('modifier', 'runtime_path', 'runtime_path');
        $this->template->registerPlugin('modifier', 'root_path', 'root_path');
    }

    /**
     * 魔术方法，供直接调用Smarty的方法
     */
    public function __call($method, $params) {
        return call_user_func_array([$this->template, $method], $params);
    }

}
