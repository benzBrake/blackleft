<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="content-type" content="text/html; charset=<?php $this->options->charset(); ?>"/>
    <title><?php $this->options->title(); ?><?php $this->archiveTitle(); ?></title>

    <!-- 使用url函数转换相关路径 -->
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo cdnUrl('style.css') ?>"/>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo cdnUrl('app.js') ?>"></script>

    <!-- 通过自有函数输出HTML头部信息 -->
    <?php $this->header(); ?>

    <!--[if lt IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo cdnUrl('ie6.css') ?>" /><![endif]-->

</head>

<body>
<div id="topperimg">
    <div id="topper" class="container_16 clearfix">
        <div id="topper_logo">
            <a href="<?php $this->options->siteUrl(); ?>"><h1><?php $this->options->title() ?></h1></a>
        </div>
        <div id="topper_nav_top"></div>
        <div id="topper_nav">
            <ul id="topper_nav_ul">
                <li<?php if ($this->is('index')): ?> class="current"<?php endif; ?>><a
                        href="<?php $this->options->siteUrl(); ?>"><?php _e('home'); ?></a></li>
                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                <li>
                    <a class="sub-toggle" href="javascript:void(0)"><?php _e("more"); ?> +</a>
                    <ul class="sub-menu">
                        <div class="sub-menu-border-top"></div>
                        <div class="sub-menu-bg"></div>
                        <?php while ($pages->next()): ?>
                            <li<?php if ($this->is('page', $pages->slug)): ?> class="current"<?php endif; ?>><a
                                    href="<?php $pages->permalink(); ?>"
                                    title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a></li>
                        <?php endwhile; ?>
                        <div class="sub-menu-border-bottom"></div>
                    </ul>
                </li>
            </ul>
            <form id="search" method="post" action="">
                <div id="topper_search"><span class="topper_search_input"><input type="text" name="s" size="20"
                                                                                 value=""/></span><span
                        class="topper_search_sub"><input type="image"
                                                         src="<?php $this->options->themeUrl('images/btn.gif'); ?>"
                                                         value=""/></span></div>
            </form>
        </div>
        <div id="topper_nav_bot"></div>
    </div>
</div>
<!-- end #header -->


<div class="container_16 clearfix" id="outmain">
