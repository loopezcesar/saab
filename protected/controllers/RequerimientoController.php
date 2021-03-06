<?php

class RequerimientoController extends Controller
{

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	public $columnas=array();




	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index','admin','create','view'),
				'expression'=>'Yii::app()->user->checkAccess("usuario")',
			),
			array('allow',
				'actions'=>array('index','admin','create','view'),
				'expression'=>'Yii::app()->user->checkAccess("almacen")',
			),
			array('allow',
				'actions'=>array('index','admin','create','view'),
				'expression'=>'Yii::app()->user->checkAccess("abastecimiento")',
			),
			array('allow',
				'actions'=>array('index','admin','create','view'),
				'expression'=>'Yii::app()->user->checkAccess("administrador")',
			),
			array('allow', 
				'actions'=>array('buscaClasificador','buscaBien','buscaMeta','addItem','details','aumentarItem','disminuirItem','removeItem','idCatalogo'),
				'users'=>array('*'),
			),
			// array('allow', // allow admin user to perform 'admin' and 'delete' actions
			// 	'actions'=>array('delete'),
			// 	'users'=>array('admin'),
			// 	'deniedCallback' => function() {Yii::app()->controller->redirect(array ('site/error'));},
			// ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		$requerimiento_bien = new RequerimientoBien();
		
		//$bienes=$this->loadModel($id);
        $requerimiento_bien->unsetAttributes();
        $requerimiento_bien->IDREQUERIMIENTO = $id;
        $usuario=Usuario::model()->findByAttributes(array('USU_usuario' => Yii::app()->user->getName()));

        $dataProvider = $requerimiento_bien->search();
        if(isset($_POST['Requerimiento']))
		{
			$model->attributes=$_POST['Requerimiento'];
			$transaction=Yii::app()->db->beginTransaction();//transacciones
			if($model->save()){
				$compra=Yii::app()->getGlobalState('comprar');
				$idcompra=Yii::app()->getGlobalState('idcomprar');
				$i=0;
				
				

				foreach($compra as $value){
							$bienes = new RequerimientoBien();
							$bienes=RequerimientoBien::model()->findByAttributes(array('IDBIEN' =>$idcompra[$i],'IDREQUERIMIENTO'=>$id ));
							$bienes->RBI_cantidadComprar=$value;			        	
				        	//$bienes->IDBIEN=$idcompra[$i];
				        	++$i;
				        	if (!$bienes->save()) {
				        		$transaction->rollBack();
				        		Yii::app()->user->setFlash('error', '<strong>Oh Nooo!</strong> No se pueden guardar lo items');
	                            //throw new Exception("Error al guardar items");
	                        }
				        

				      }
				$transaction->commit();
				$this->redirect(array('admin'));
				Yii::app()->clearGlobalState('comprar');
				Yii::app()->clearGlobalState('idcomprar');
				
			}
				
		}
        
        if ($usuario->IDUSUARIO==1 || $model->IDUSUARIO == $usuario->IDUSUARIO) {
        	$this->render('view',array(
        		'model'=>$model,
        		'dataProvider'=>$dataProvider,
        		));
        }else{
        	throw new CHttpException(403,'Usted no se encuentra autorizado para acceder a requerimientos que no son suyos. Por que lo hace?');
        }
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */


	public function actionCreate()
	{
		Yii::app()->setGlobalState('site_id', 0);



		$model=new Requerimiento;
		$idusuario = Yii::app()->user->getState('idusuario');
  		$usuario= new Usuario;
 		$usuario = Usuario::model()->findByPk($idusuario);
 		$clasificador=	new clasificador('search');

 		$col=Yii::app()->getGlobalState('arrays');
 		
 		$clasificador->unsetAttributes();
 		$catalogo=new Catalogo;
 		$catalogo->unsetAttributes();

 		$meta=new Meta;
 		$meta->unsetAttributes();

 		/*  

			$requerimiento_bien= array(modelo);


 		*/
        // $clasificador = new Clasificador;
        // $clasificador= Clasificador::model()->findAll();
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);
		if($this->validador()){
			if(isset($_POST['Requerimiento']))
			{
				$model->attributes=$_POST['Requerimiento'];

				// $transaction=Yii::app()->db->beginTransaction();//transacciones
				if($model->save()){


				      for($x=0;$x<count($col); $x++){
				        $requerimiento_bien= new RequerimientoBien; 
				        if(!empty($col[$x][0])){
				        	$requerimiento_bien->IDREQUERIMIENTO=$model->IDREQUERIMIENTO;
				        	$requerimiento_bien->IDBIEN=$col[$x][0];
				        	$requerimiento_bien->RBI_cantidad=$col[$x][1];
				        	if (!$requerimiento_bien->save()) {
				        		// $transaction->rollBack();
	                            Yii::app()->user->setFlash('error', '<strong>Oh Nooo!</strong> No se pueden guardar lo items');
	                        }
	                        else{
	                        	// $transaction->commit();
	                        		
	                        }
	                        
				        }

				      }
				    Yii::app()->clearGlobalState('arrays');
					$this->redirect(array('view','id'=>$model->IDREQUERIMIENTO));
					
				}

			}
		}
		else{
			
			// Yii::app()->user->setFlash('info', '<strong>Heyy!</strong> ');
			Yii::app()->user->setFlash('warning', '<strong>Atencion!</strong> debe ingresar algun bien');
			// Yii::app()->user->setFlash('error', '<strong>Oh Nooo!</strong> No se pueden guardar lo items');
		}




		$this->render('create',array(
			'model'=>$model,
			'usuario'=>$usuario,
			'clasificador'=>$clasificador,
			'catalogo'=>$catalogo,
			'meta'=>$meta,
		));
	}

	public function actionBuscaClasificador() {

       //$q = $_GET['busca_clasificador'];
       $q=trim($_GET['term']);

       //$q='LA';

       if (isset($q)) {
           $criteria = new CDbCriteria;
           
   			
           // condition to find your data, using q as the parameter field
           $criteria->condition = "CLA_descripcion LIKE '%". $q ."%'";
           //$criteria->order = 'CLA_descripcion'; // correct order-by field
           $criteria->limit = 10; // probably a good idea to limit the results
           // with trailing wildcard only; probably a good idea for large volumes of data
           //$criteria->params = array(':q' => trim($q) . '%'); 
           $clasificador= Clasificador::model()->findAll($criteria);

           if (!empty($clasificador)) {
               $returnVal = '';
               $out = array();
               foreach ($clasificador as $p) {
                   $out[] = array(
                      // expression to give the string for the autoComplete drop-down
                       'label' => $p->CLA_descripcion,  
                       'value' => $p->CLA_descripcion,
                       'id' => $p->CODIGOCLASIFICADOR, // return value from autocomplete
                   );
                }
              echo CJSON::encode($out);
              Yii::app()->end();
            }
        }
    }
    public function actionIdCatalogo(){
    	$idcat= $_POST['idclasificador'];
    	Yii::app()->setGlobalState('idclasificador', $idcat);
    }

    public function actionBuscaBien() {

       //$q = $_GET['busca_clasificador'];
    	$q=trim($_GET['term']);
    	//echo 'funciona el valor es:'.$_GET['term'];
       //$q='LA';

    	if (isset($q)) {
    		$condicion = new CDbCriteria;
           // condition to find your data, using q as the parameter field
    		$condicion->condition = "IDCATALOGO>=4898 AND length(CAT_codigo)=12  AND CAT_descripcion LIKE '%". $q ."%' order by CAT_descripcion";
           //$condicion->order = 'CLA_descripcion'; // correct order-by field
           $condicion->limit = 10; // probably a good idea to limit the results
           // with trailing wildcard only; probably a good idea for large volumes of data
           //$condicion->params = array(':q' => trim($q) . '%'); 
           $catalogo=  Catalogo::model()->findAll($condicion);


           if (!empty($catalogo)) {
           	$returnVal = '';
           	$salida = array();
           	foreach ($catalogo as $c) {
           		$salida[] = array(
                      // expression to give the string for the autoComplete drop-down
           			'label' => $c->CAT_descripcion,  
           			'value' => $c->CAT_descripcion,
           			'unidad'=>$c->CAT_unidad,
                     'id' => Bien::model()->findByAttributes(array('IDCATALOGO'=>$c->IDCATALOGO))->IDBIEN, // return value from autocomplete
                       );
           	}
           	echo CJSON::encode($salida);
           	Yii::app()->end();
           }
       }
   	}

   	public function actionBuscaMeta() {

       //$q = $_GET['busca_clasificador'];
       $q=trim($_GET['term']);

       //$q='LA';

       if (isset($q)) {
           $criteria = new CDbCriteria;
           // condition to find your data, using q as the parameter field
           $criteria->condition = "MET_descripcion LIKE '%". $q ."%'";
           //$criteria->order = 'CLA_descripcion'; // correct order-by field
           $criteria->limit = 10; // probably a good idea to limit the results
           // with trailing wildcard only; probably a good idea for large volumes of data
           //$criteria->params = array(':q' => trim($q) . '%'); 
           $meta= Meta::model()->findAll($criteria);

           if (!empty($meta)) {
               $returnVal = '';
               $out = array();
               foreach ($meta as $m) {
                   $out[] = array(
                      // expression to give the string for the autoComplete drop-down
                       'label' => $m->MET_descripcion,  
                       'value' => $m->MET_descripcion,
                       'id' => $m->CODMETA, // return value from autocomplete
                   );
                }
              echo CJSON::encode($out);
              Yii::app()->end();
            }
        }
    }
   
   
    public function actionAddItem() {

        try {
        	
        	$idbien= $_POST['idbien'];
            $rbi_cantidad= $_POST['rbi_cantidad'];
            $descripcion= $_POST['descripcion'];
           
            
            // Yii::app()->params['valor']
         
        	//Yii::app()->setGlobalState(string $key, mixed $defaultValue=NULL);

        	
			$i=Yii::app()->getGlobalState('site_id'); //obtiene el valor de una variable global
          	
          if($i==0){
          	
          	$this->columnas[$i][0]=$idbien;
            $this->columnas[$i][1]=$rbi_cantidad;
            $this->columnas[$i][2]=$descripcion;
            // array_push($this->columnas,array($i=>$idbien));
            // array_push($this->columnas,array($i=>$rbi_cantidad));
            // array_push($this->columnas,array($i=>$descripcion));
            Yii::app()->setGlobalState('arrays', $this->columnas);
          }else{
          	$this->columnas=Yii::app()->getGlobalState('arrays');
          	$this->columnas[$i][0]=$idbien;
            $this->columnas[$i][1]=$rbi_cantidad;
            $this->columnas[$i][2]=$descripcion;
          	// array_push($this->columnas,array($i=>$idbien));
           //  array_push($this->columnas,array($i=>$rbi_cantidad));
           //  array_push($this->columnas,array($i=>$descripcion));
            Yii::app()->setGlobalState('arrays', $this->columnas);
          }

            ++$i;

           	Yii::app()->setGlobalState('site_id', $i);	// envia valor a una varible global
            
            //clearGlobalState()
            $this->actionDetails();
            
            //echo 'valor uno:'.$this->columnas[0][0].'valor ahora:'.$this->columnas[1][0].'  variable '.$i;
            

            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function actionDetails() {
        
        $this->renderPartial('_details');
    }

    public function busqueda($id){
    	

    	$this->columnas=Yii::app()->getGlobalState('arrays');
    	// foreach ($this->columnas as $col) {
    	for($i=0;$i<count($this->columnas); $i++){
    		
    		if(stristr($this->columnas[$i][0],$id))    			
    			break;
    		
    	}
    	return $i;
    }
    public function validador(){
    	$col=Yii::app()->getGlobalState('arrays');
	    $columns=array();
	      for($x=0;$x<count($col); $x++){
	        
	        if(!empty($col[$x][0]) && !empty($col[$x][1]) && !empty($col[$x][2]))
	         	return true;
	      	else
	      		return false;
	      }
	}

    public function actionAumentarItem() {

    	$id= $_POST['idbien'];
    	$this->columnas=Yii::app()->getGlobalState('arrays');
    	$valor=-1;
    	
    	$valor=$this->busqueda($id);
        
        ++$this->columnas[$valor][1];
        Yii::app()->setGlobalState('arrays', $this->columnas);
		//echo $valor.' bien:'.$id.' valor:'.$this->columnas[$valor][0];
        $this->actionDetails();
    }
    public function actionDisminuirItem() {

        $id= $_POST['idbien'];
    	$valor=-1;    	
        $valor=$this->busqueda($id);
        $this->columnas=Yii::app()->getGlobalState('arrays');
        if($this->columnas[$valor][1]<=1){
         	unset($this->columnas[$valor][0]);
        	unset($this->columnas[$valor][1]);
       		unset($this->columnas[$valor][2]);       	
        }
        else{
        	--$this->columnas[$valor][1];
        }
        
        Yii::app()->setGlobalState('arrays', $this->columnas);

        $this->actionDetails();
    }

    public function actionRemoveItem() {

    	$id= $_POST['idbien'];
    	$valor=-1;
        $valor=$this->busqueda($id);
        $this->columnas=Yii::app()->getGlobalState('arrays');
        unset($this->columnas[$valor][0]);
        unset($this->columnas[$valor][1]);
        unset($this->columnas[$valor][2]);
        $this->columnas = array_values($this->columnas);
        Yii::app()->setGlobalState('arrays', $this->columnas);

        $this->actionDetails();

    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=new Requerimiento;
		$idusuario = Yii::app()->user->getState('idusuario');
  		$usuario= new Usuario;
 		$usuario = Usuario::model()->findByPk($idusuario);
        
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Requerimiento']))
		{
			$model->attributes=$_POST['Requerimiento'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->IDREQUERIMIENTO));
		}

		$this->render('update',array(
			'model'=>$model,
			'usuario'=>$usuario,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	// public function actionIndex()
	// {
	// 	$dataProvider=new CActiveDataProvider('Requerimiento');
	// 	$this->render('index',array(
	// 		'dataProvider'=>$dataProvider,
	// 	));
	// }

	public function actionIndex()
	{
		//aca es para el index, que redirecciona a views/requerimiento/admin.php
		Yii::app()->setGlobalState('arrays', $this->columnas);
        $requerimiento = new Requerimiento('search');//creo una variable con la funcion search de Requerimiento
        $requerimiento->unsetAttributes();//limpio los valores que pueda tener
        $requerimiento->IDUSUARIO = Yii::app()->user->getState('idusuario');//a la variable le asigno el IDUSUARIO igual que el que esta logeado
        
        $dataProvider = $requerimiento->search();//creo un dataprovider que mandaremos al admin y ese dataprovider solo tendra los requerimientos del usuario logeado
        
		//$dataProvider=new CActiveDataProvider('Requerimiento');//,array('pagination'=>array('pageSize'=>2,),));//,array('criteria'=>array('condition'=>'IDUSUARIO=2'))
		// $dataProvider->setPagination('1');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		
		Yii::app()->clearGlobalState('arrays');
		$model=new Requerimiento('search');
		$model->unsetAttributes();  // clear any default values
		//para la accion admin, solo le cree esto, si el idusuario es diferente del admin, los filtro; sino que se muestren todos
		if (Yii::app()->user->getState('idusuario')!=1) {
			$model->IDUSUARIO = Yii::app()->user->getState('idusuario');
		}

		if(isset($_GET['Requerimiento']))
			$model->attributes=$_GET['Requerimiento'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Requerimiento::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='requerimiento-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}