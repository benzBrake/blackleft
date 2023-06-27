<?php $this->need('header.php'); ?>

    <div class="grid_10" id="content">
	
	<?php while($this->next()): ?>
			<div class="post_title">
			     <span class="post_title_comments"><a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('0', '1', '%d'); ?></a></span>
				 <span class="post_title_title"><h2><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2></span>
				 <span class="post_title_date"><?php $this->date('j M Y'); ?></span>
			</div>
			<div class="content_top"></div>
			<div class="post">
			     <?php $this->content(''); ?>
			     <div class="post_footer"><a href="<?php $this->permalink() ?>" class="post_link_more"></a><a href="<?php $this->permalink() ?>#comments" class="post_link_comments"></a></div>
			</div>			
		    <div class="content_bot"></div>
	<?php endwhile; ?>

        <div class="content_top"></div>
		<div class="page">
		<ol class="pages clearfix">
            页码：<?php $this->pageNav(); ?>
        </ol>
		</div>
		<div class="content_bot"></div>

    
	</div><!-- end #content-->
	<?php $this->need('sidebar.php'); ?>
	<?php $this->need('footer.php'); ?>