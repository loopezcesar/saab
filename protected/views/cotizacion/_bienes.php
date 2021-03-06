<?php
$columns=array();
$i=0;
$dataProvider=$requerimiento_bien->search();

array_push($columns, array(
	'header' => 'N°',
	'value' => function($data) use(&$i){
		return ++$i;
	},
	)
);


array_push($columns, array(
	'header' => 'Bien',
	'value'=>'$data->bien->iDCATALOGO->CAT_descripcion',
	)
);

array_push($columns, array(
	'header' => 'Unidad',
	'value'=>'$data->bien->iDCATALOGO->CAT_unidad',
	)
);

array_push($columns, array(
	'header' => 'Cantidad',
	'htmlOptions'=>array('width'=>'1em'),
	'value'=>'$data->RBI_cantidad',
	)
);

array_push($columns, array(
	'header'=>'Precio unitario',
	'htmlOptions'=>array('width'=>'1em'),
	'type' => 'raw',
	'value' => function($data) {
		return CHtml::textField('precioUnitario[]','',array('style'=>'width:6em;','pattern'=>'[0-9]+(\.[0-9]{1,4}?)?'));
	},
	)
);

array_push($columns, array(
	'header'=>'Características',
	'htmlOptions'=>array('width'=>'10em'),
	'type' => 'raw',
	'value' => function($data) {
		return CHtml::textField('caracteristica[]','',array('style'=>'width:10em;'));
	},
	)
);

array_push($columns, array(
	'header'=>'Marca',
	'htmlOptions'=>array('width'=>'10em'),
	'type' => 'raw',
	'value' => function($data) {
		return CHtml::textField('marca[]','',array('style'=>'width:10em;'));
	},
	)
);

// array_push($columns, array(
// 	'header' => 'Sub Total',
// 	'value'=>'',
// 	)
// );
?>
<hr>
<br>
<h3>Ingresar los detalles de los bienes</h3>
<br>
<!-- <div class="control-group pull-right">
	<label class="control-label" for="ruc">R.U.C.:</label>
	<div class="controls"><p>gggg</p></div>
</div>
<div class="control-group">
	<label class="control-label" for="solicitante">Señor(es):</label>
	<div class="controls"><p>11111111111</p></div>
</div> -->
<?php

$this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'bordered',
	'dataProvider'=>$dataProvider,
	'template'=>"{items}",
	'columns'=>$columns,
	)
);
?>