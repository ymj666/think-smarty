# think-smarty
ThinkPHP6/ThinkPHP8 Smarty ģ������������ʹ�� Smarty5 ��Ϊģ������

## ��װ
```bash
composer require ymj666/think-smarty
```

## ����
�����ļ������� ThinkPHP �� `view.php` �����ļ���Ҫʹ�� think-smarty ���Ƚ� `view.php` �е� `type` �������޸�Ϊ `ThinkSmarty::class`

���Ӻ��޸��˼��������
- `tpl_cache`: �Ƿ���ģ����뻺�棬��Ϊ `false` ��ÿ�ζ������±��룬Ĭ��Ϊ `true`
- `tpl_cache_path`: ģ����뻺��Ŀ¼����Ӧ Smarty �� `CompileDir` ���ã�Ĭ��Ϊ `runtime/temp`
- `display_cache`: �Ƿ���ģ����Ⱦ���棬����Ϊ `true` �򻺴����ǰ��ֱ�Ӷ�ȡ��Ⱦ��������Ĭ��Ϊ `false`
 - `display_cache_path`: ģ����Ⱦ����Ŀ¼����Ӧ Smarty �� `CacheDir` ���ã�Ĭ��Ϊ `runtime/display_cache`
 - `display_cache_time`: ģ����Ⱦ������Ч�ڣ���λ���롣����Ϊ `0` ���������ڣ�Ĭ��Ϊ `3600`
 - `tpl_replace_string`: ģ������滻����һ�����飬��ʽΪ `'ԭ�ı�' => '�滻���ı�'`���磺`'__STATIC__'  => '/static'`

�����������鿴 `ThinkSmarty.php`

## ʹ��
�� Smarty ģ���ļ��п���ֱ��ʹ�� ThinkPHP �Ĳ������ֺ���������ɲ鿴 `ThinkSmarty.php`
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

## �������
- [ThinkPHP8 �ĵ�](https://doc.thinkphp.cn/v8_0)
- [Smarty �ĵ�](https://smarty-php.github.io/smarty/)

## ����˵��
����ο��� ThinkPHP8 ���õ� PHP ģ������
