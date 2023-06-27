
    <div id="sidebar_plan"><img src="<?php $this->options->themeUrl(''); ?>/images/plane.png"></div>
	
	<div class="grid_4" id="sidebar">	    

        <div class="widget">
			<h3><?php _e('Category'); ?></h3>
            <ul>
                <?php $this->widget('Widget_Metas_Category_List')
                ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
            </ul>
		</div>

    
	    <div class="widget" id="new-comments">
			<h3><?php _e('Comments'); ?></h3>
            <ul>
            <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
            <?php while($comments->next()): ?>
                <li><?php $comments->gravatar(40, 'X', '', 'avatar'); ?><a href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?></a><br><?php $comments->excerpt(25, '...'); ?></li>
            <?php endwhile; ?>
            </ul>
	    </div>



        <div class="widget">
			<h3><?php _e('Archives'); ?></h3>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=F Y')
                ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
            </ul>
		</div>

		<div class="widget">
			<h3><?php _e('Other'); ?></h3>
            <ul>
                <?php if($this->user->hasLogin()): ?>
					<li class="last"><a href="<?php $this->options->adminUrl(); ?>"><?php _e('进入后台'); ?> (<?php $this->user->screenName(); ?>)</a></li>
                    <li><a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a></li>
                <?php else: ?>
                    <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>"><?php _e('登录'); ?></a></li>
                <?php endif; ?>
                <li><a href="http://validator.w3.org/check/referer">Valid XHTML</a></li>
                <li><a href="http://www.typecho.org">Typecho</a></li>
            </ul>
		</div>

    </div><!-- end #sidebar -->
