<ul class="navigation">
	<?php foreach($blog_widget as $post_widget): ?>
		<li><?php echo anchor(get_post_url($post_widget->id, $post_widget->slug, $post_widget->created_on, $post_widget->category_id), $post_widget->title); ?></li>
	<?php endforeach; ?>
</ul>