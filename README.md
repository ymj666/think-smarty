# think-smarty
ThinkPHP6/ThinkPHP8 Smarty 模板引擎驱动，使用 Smarty5 作为模板引擎

## 安装
```bash
composer require ymj666/think-smarty
```

## 配置
配置文件沿用了 ThinkPHP 的 `view.php` 配置文件，要使用 think-smarty 请先将 `view.php` 中的 `type` 配置项修改为 `ThinkSmarty::class`

增加和修改了几个配置项：
- `tpl_cache`: 是否开启模板编译缓存，设为 `false` 则每次都会重新编译，默认为 `true`
- `tpl_cache_path`: 模板编译缓存目录，对应 Smarty 的 `CompileDir` 配置，默认为 `runtime/temp`
- `display_cache`: 是否开启模板渲染缓存，设置为 `true` 则缓存过期前会直接读取渲染结果输出，默认为 `false`
 - `display_cache_path`: 模板渲染缓存目录，对应 Smarty 的 `CacheDir` 配置，默认为 `runtime/display_cache`
 - `display_cache_time`: 模板渲染缓存有效期，单位：秒。设置为 `0` 则永不过期，默认为 `3600`
 - `tpl_replace_string`: 模板输出替换，是一个数组，格式为 `'原文本' => '替换的文本'`，如：`'__STATIC__'  => '/static'`

更多配置项，请查看 `ThinkSmarty.php`

## 使用
在 Smarty 模板文件中可以直接使用 ThinkPHP 的部分助手函数，具体可查看 `ThinkSmarty.php`
- `cache`
- `config`
- `cookie`
- `env`
- `input`
- `lang`
- `parse_name`
- `session`
- `token`
- `token_field`
- `token_meta`
- `url`
- `app_path`
- `base_path`
- `config_path`
- `public_path`
- `runtime_path`
- `root_path`

## 相关链接
- [ThinkPHP8 文档](https://doc.thinkphp.cn/v8_0)
- [Smarty 文档](https://smarty-php.github.io/smarty/)

## 其他说明
代码参考了 ThinkPHP8 内置的 PHP 模板引擎
