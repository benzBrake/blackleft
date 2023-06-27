<?php
/**
 * 获取gravatar头像地址
 *
 * @param String $mail 邮箱地址
 * @param mixed $gravatarOptions 配置
 * @return String
 * @date 2020-09-02
 */
function gravatarUrl($mail = null, $gravatarOptions = null)
{
    $gravatarOptions = Typecho_Config::factory($gravatarOptions);
    $gravatarOptions->setDefault(array(
        'prefix' => 'https://cravatar.cn/avatar/',
        'size' => '32',
        'rating' => Helper::options()->commentsAvatarRating, // 头像分级
        'default' => 'mp', // Gravatar没有头像时的默认头像
    ), false);

    $url = '';
    if (!empty($gravatarOptions->prefix)) {
        $url = $gravatarOptions->prefix;
    } else if (defined('__TYPECHO_GRAVATAR_PREFIX__')) {
        $url = __TYPECHO_GRAVATAR_PREFIX__;
    } else {
        $url = 'https://cravatar.cn/avatar/';
    }


    if (!empty($mail)) {
        $url .= md5(strtolower(trim($mail)));
    }
    $url .= '?s=' . $gravatarOptions->size;
    $url .= '&amp;r=' . $gravatarOptions->rating;
    $url .= '&amp;d=' . $gravatarOptions->default;

    return $url;
}

/**
 * 从 Widget_Options 对象获取 Typecho 选项值（文本型）
 * @param string $key 选项 Key
 * @param mixed $default 默认值
 * @param string $method 测空值方法
 * @return string
 */
function getStrConf($key, $default = '', $method = 'empty')
{
    $value = Helper::options()->$key;
    if ($method === 'empty') {
        return empty($value) ? $default : $value;
    } else {
        return call_user_func($method, $value) ? $default : $value;
    }
}


/**
 * 获取文章摘要
 * @param Typecho_Widget|Widget_Archive|Widget_Abstract_Contents $item
 * @param int|null $length 长度
 * @param string $trim 结尾
 * @return string
 */
function getAbstract($item, $length = null, $trim = '...')
{
    $content = $item->excerpt;
    $length = $length == null ? getStrConf('abstractLength', 300) : $length;
    $content = preg_replace('#(<img\s[^>]*)(\balt=)("?)([^"]+)("?)([^>]*>?)#', _t("【图片 %s】", '$4'), $content);
    $abstract = Typecho_Common::subStr(strip_tags($content), 0, $length, $trim);
    if ($item->password) {
        $abstract = _t(_t("加密文章，请前往内页查看详情"));
    }
    if (empty($abstract)) $abstract = _t("暂无简介");
    return $abstract;
}

function cdnUrl($uri = '') {
    $prefix = getStrConf('cdnPrefix');
    if (strpos($prefix, 'http') === false && strpos($prefix, '//') === false) {
        $prefix = Helper::options()->themeUrl;
    }
    $prefix = rtrim($prefix, "/") . "/";
    return Typecho_Common::url($uri, $prefix);
}

function themeConfig($form) {
    $cdnPrefix = new Typecho_Widget_Helper_Form_Element_Text('cdnPrefix', null, null, _t('静态资源 CDN 前缀'), _t('不懂请留空'));
    $form->addInput($cdnPrefix);  // 添加输入框到表单

    $footerHTML = new Typecho_Widget_Helper_Form_Element_Textarea('footerHTML', null, null, _t('附加尾部 HTML 代码'), _t('不懂请留空'));
    $form->addInput($footerHTML);  // 添加输入框到表单
}
