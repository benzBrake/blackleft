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
