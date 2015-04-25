<?php defined('SYSPATH') or die('No direct access allowed.');?>

<?php if ($paginator !== FALSE):?>
	<div class="paginator" id="pages"></div>
	<script type="text/javascript">
		pages = new Paginator(
					"pages",
					<?php echo $paginator['page_count']; ?>,
					10,
					<?php echo $paginator['current']; ?>,
					'<?php echo $paginator['link']; ?>'
				);
		$(document).ready(function(){
			$('.page-control').hide();
		});
	</script>

	<div class="page-control">
		<?php if (isset($paginator['previous'])):?>
			<a href="<?php echo $paginator['previous']; ?>" class="prev">prev</a>
		<?php endif;?>
		<ul class="list">
		<?php foreach ($paginator['items'] as $item):?>
			<?php
				$class = '';
				if (isset($item['current']))
				{
					$class = 'active';
				}
			?>
			<li class="<?php echo $class; ?>">
				<?php if ($class == '' AND ! empty($item['link'])):?>
					<a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a>
				<?php else:?>
					<span><?php echo $item['title']; ?></span>
				<?php endif;?>
			</li>
		<?php endforeach;?>
		</ul>
		<?php if (isset($paginator['next'])):?>
			<a href="<?php echo $paginator['next']; ?>" class="next">next</a>
		<?php endif;?>
	</div>

<?php endif;?>