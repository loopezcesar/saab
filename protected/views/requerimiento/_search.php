<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	

	<div class="span8 input-append">
		<?php echo $form->textFieldRow($model,'IDREQUERIMIENTO',
			array(
				'class'=>'span4',
				'placeholder' => 'Nro Requerimiento',
				 // 'labelHtmlOptions' => array('label' => false)
			)
		); ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',			
			'icon'=>'icon-search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>


