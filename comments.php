<?php

function threadedComments($comments)
{
?>
    <li id="<?php $comments->theId(); ?>"<?php $comments->levelsAlt('', ' class="odd"'); ?>>
					<div class="comment_data">
						<?php $comments->gravatar(32, 'X', '', 'avatar'); ?>
						<span class="author"><?php $comments->author(); ?></span>
						<?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?> <!-- <span class="count">#<?php echo $comments->sequence(); ?></span> -->
					</div>
					<?php $comments->content(); ?>
                    <?php $comments->threadedComments('<ol>', '</ol>'); ?>
                    <?php if (!$comments->isTopLevel): ?>
                    <div class="comment_reply">
                        <?php Helper::replyLink($comments->theId, $comments->coid, 'Reply', 'respond'); ?>
                    </div>
                    <?php endif; ?>
    </li>
<?php
}
?>

<div id="comments">
            <?php $this->comments()->to($comments); ?>
            <?php if ($comments->have()): ?>   
			
            <?php while($comments->next()): ?>
				<div id="<?php $comments->theId(); ?>" class="clearfix">
				     <div class="comments_img"><?php $comments->gravatar(78, 'X', '', 'avatar'); ?></div>
					 <div class="comments_header"></div>
					 <div class="comments_node"><p class="comments_date"><?php $comments->author(); ?> <?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?></p><?php $comments->content(); ?></div>
					 <div class="comments_footer"></div>
				</div>
			<?php endwhile; ?>

            
            <?php endif; ?>

            <?php if($this->allow('comment')): ?>
            <div id="respond">
			<div class="content_top"></div>
            <div class="post">
			<form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
                <?php if($this->user->hasLogin()): ?>
				<p>Logged in as <a href="<?php $this->options->adminUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('登出'); ?> &raquo;</a></p>
                <?php else: ?>
				<p>
                    <label for="author"><?php _e('称呼'); ?><span class="required">*</span></label>
					<input type="text" name="author" id="author" class="text" size="15" value="<?php $this->remember('author'); ?>" />
				</p>
				<p>
                    <label for="mail"><?php _e('E-mail'); ?><?php if ($this->options->commentsRequireMail): ?><span class="required">*</span><?php endif; ?></label>
					<input type="text" name="mail" id="mail" class="text" size="15" value="<?php $this->remember('mail'); ?>" />
				</p>
				<p>
                    <label for="url"><?php _e('网站'); ?><?php if ($this->options->commentsRequireURL): ?><span class="required">*</span><?php endif; ?></label>
					<input type="text" name="url" id="url" class="text" size="15" value="<?php $this->remember('url'); ?>" />
				</p>
                <?php endif; ?>
				<p><textarea rows="5" cols="50" name="text" class="textarea"><?php $this->remember('text'); ?></textarea></p>
				<p><input type="image" src="<?php $this->options->themeUrl('images/submit.gif'); ?>" value="" /></p>
			</form>
            </div>
			<div class="content_bot"></div>
            <?php else: ?>
            <h4><?php _e('评论已关闭'); ?></h4>
            <?php endif; ?>
			</div>
		</div>
