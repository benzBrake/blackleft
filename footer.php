<div class="grid_14" id="footer">
    <a href="<?php $this->options->siteurl(); ?>"><?php $this->options->title(); ?></a> <?php _e('is powered by'); ?> <a
        href="http://www.typecho.org">Typecho)))</a> Desgin by <a href="http://www.obox-design.com">obox-design</a> Css
    by <a href="http://www.ak92.com">ak92.com</a> & <a href="http://samto.cn">samto.cn</a> & <a href="https://xiamp.net">xiamp.net</a><br/><a
        href="<?php $this->options->feedUrl(); ?>"><?php _e('文章'); ?> RSS</a> and <a
        href="<?php $this->options->commentsFeedUrl(); ?>"><?php _e('评论'); ?> RSS</a>
</div><!-- end #footer -->
</div>
<script src="<?php echo cdnUrl('app.js') ?>"></script>
<?php if (is_file(Helper::options()->themeFile('status/system_usage.json'))): ?>
<?php endif; ?>
<?php $this->footer(); ?>
<?php echo getStrConf('footerHTML'); ?>
</body>
</html>
