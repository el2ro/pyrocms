<?php if(is_array($categories)): ?>
<ul>
	<?php foreach($categories as $category): ?>
	<li>
		<?php echo anchor(get_category_url($category->slug), $category->title); ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
