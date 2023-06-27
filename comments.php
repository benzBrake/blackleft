<?php

function threadedComments($comments, $options)
{
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        } else {
            $commentClass .= ' comment-by-user';
        }
    }
    $commentLevelClass = $comments->levels > 0 ? ' comment-child' : ' comment-parent';
    ?>
    <div id="<?php $comments->theId(); ?>" class="clearfix<?php echo $commentClass . $commentLevelClass; ?>">
        <div class="comments_img"><img class="avatar" src="<?php echo gravatarUrl($comments->mail, "size=78"); ?>">
        </div>
        <div class="comments_header"></div>
        <div class="comments_node">
            <span class="author"><?php $comments->author(); ?></span>
            <?php $comments->date('F jS, Y'); ?> at <?php $comments->date('h:i a'); ?>
            <div class="comment_content">
                <?php $comments->content(); ?>
            </div>
        </div>
        <div class="comments_footer"></div>
        <div class="comment_reply">
            <?php $comments->reply('Reply', 'respond') ?>
        </div>
        <div class="comments_children clearfix"><?php $comments->threadedComments('<ol>', '</ol>'); ?></div>
    </div>
    <?php
}

?>

<div id="comments">
    <?php $this->comments()->to($comments); ?>
    <?php if ($comments->have()): ?>
        <?php $comments->listComments(); ?>
        <?php $comments->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
    <?php endif; ?>

    <?php if ($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="comment_respond">
        <div class="content_top"></div>
        <div class="post">
            <form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
                <div class="comment_cancel"><?php $comments->cancelReply('Cancel'); ?></div>
                <?php if ($this->user->hasLogin()): ?>
                    <p>Logged in as <a
                            href="<?php $this->options->adminUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a
                            href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('登出'); ?> &raquo;</a>
                    </p>
                <?php else: ?>
                    <p>
                        <label for="author"><?php _e('称呼'); ?><span class="required">*</span></label>
                        <input type="text" name="author" id="author" class="text" size="15"
                               value="<?php $this->remember('author'); ?>"/>
                    </p>
                    <p>
                        <label for="mail"><?php _e('E-mail'); ?><?php if ($this->options->commentsRequireMail): ?><span
                                class="required">*</span><?php endif; ?></label>
                        <input type="text" name="mail" id="mail" class="text" size="15"
                               value="<?php $this->remember('mail'); ?>"/>
                    </p>
                    <p>
                        <label for="url"><?php _e('网站'); ?><?php if ($this->options->commentsRequireURL): ?><span
                                class="required">*</span><?php endif; ?></label>
                        <input type="text" name="url" id="url" class="text" size="15"
                               value="<?php $this->remember('url'); ?>"/>
                    </p>
                <?php endif; ?>
                <p><textarea rows="5" cols="50" name="text"
                             class="textarea"><?php $this->remember('text'); ?></textarea></p>
                <p><input type="image" src="<?php $this->options->themeUrl('images/submit.gif'); ?>" value=""/></p>
            </form>
        </div>
        <div class="content_bot"></div>
        <?php else: ?>
            <h4><?php _e('评论已关闭'); ?></h4>
        <?php endif; ?>
    </div>
</div>
