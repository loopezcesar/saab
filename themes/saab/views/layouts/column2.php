<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>

<div class="row-fluid">
	<div class="span10">
		<div id="sidebar pull-right">
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>'',
			));
			
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'operations inline pull-right'),
			));
			$this->endWidget();
		?>
		</div><!-- sidebar -->
	</div>

</div>
<<<<<<< HEAD
<hr>
=======

>>>>>>> origin/saabDavid
<div class="row-fluid">

	<div class="span8 offset2">

		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	
</div>

<?php $this->endContent(); ?>