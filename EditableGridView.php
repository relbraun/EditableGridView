<?php

Yii::import('zii.widgets.grid.CGridView');
Yii::import('ext.EditableGridView.EditableGridColumn');

/**
 * Enable you to create editable grid that it beeing updated at real time using AJAX requests.
 * in order to make it working you have to configure it in the main.php config file and
 * put the following content:
 * <pre>
 * 'controllerMap' => array(
            'editableGrid' => 'ext.EditableGridView.EditableGridController'
        ),
 * </pre>
 *
 * The using is like this:
 *
 * <pre>
 * $this->widget('ext.EditableGridView.EditableGridView', array(
 *     'dataProvider'=>$dataProvider,
 *     'ownScriptUri'=>'url/to/scipt.js', //optional
 *     'action'=>'action name' //optional
 *     'defaultController'=>'some controller' //optional
 *     'columns'=>array(
 *         'title',          // display the 'title' attribute
 *         'category.name',  // display the 'name' attribute of the 'category' relation
 *         'content:html',   // display the 'content' attribute as purified HTML
 *         array(            // display 'create_time' using an expression
 *             'name'=>'create_time',
 *             'value'=>'date("M j, Y", $data->create_time)',
 *         ),
 *         array(            // display 'author.username' using an expression
 *             'name'=>'authorName',
 *
 *             'value'=>'$data->author->username',
 *         ),
 *          array(            // display dropdown list with data using function
 *             'name'=>'category',
 *             'field_type'=>'dropdown',
 *             'dropDownData'=>'$data->listCategory()',
 *         ),
 *         array(            // display a column with "view", "update" and "delete" buttons
 *             'class'=>'CButtonColumn',
 *         ),
 *     ),
 * ));
 *
 * </pre>
 * Good luck!
 *
 * @author Kalman
 */
class EditableGridView extends CGridView
{

    public $ownScriptUri;

    public $action = 'ajaxUpdate';

    private $grid_id;

    public $defaultController = 'editableGrid';

    public function init()
    {
        Yii::setPathOfAlias('EGV',__DIR__);
        Yii::app()->controllerMap['editableGrid']='EGV.EditableGridController';

        if(empty($this->updateSelector))
			throw new CException(Yii::t('zii','The property updateSelector should be defined.'));
		if(empty($this->filterSelector))
			throw new CException(Yii::t('zii','The property filterSelector should be defined.'));
                $this->grid_id = get_class($this->dataProvider->data[0]) . '-editable-grid';
		if(!isset($this->htmlOptions['class']))
			$this->htmlOptions['class']='grid-view';

                if($this->baseScriptUrl===null)
			$this->baseScriptUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview';

		$this->ownScriptUri=Yii::app()->getAssetManager()->publish(__DIR__);

		$this->cssFile=$this->ownScriptUri.'/style.css';
		Yii::app()->getClientScript()->registerCssFile($this->cssFile);

		$this->initColumns();
    }

    protected function initColumns()
	{
		if($this->columns===array())
		{
			if($this->dataProvider instanceof CActiveDataProvider)
				$this->columns=$this->dataProvider->model->attributeNames();
			elseif($this->dataProvider instanceof IDataProvider)
			{
				// use the keys of the first row of data as the default columns
				$data=$this->dataProvider->getData();
				if(isset($data[0]) && is_array($data[0]))
					$this->columns=array_keys($data[0]);
			}
		}
		$id=$this->getId();

		foreach($this->columns as $i=>$column)
		{

			if(is_string($column)){
				$column=$this->createDataColumn($column);

                        }
			else
			{

				if(!isset($column['class']))
					$column['class']='EditableGridColumn';
				$column=Yii::createComponent($column, $this);
			}
			if(!$column->visible)
			{
				unset($this->columns[$i]);
				continue;
			}
			if($column->id===null)
				$column->id=$id.'_c'.$i;
			$this->columns[$i]=$column;
		}

		foreach($this->columns as $column)
			$column->init();
	}

        protected function createDataColumn($text)
	{
		if(!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/',$text,$matches))
			throw new CException(Yii::t('zii','The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
		$column=new EditableGridColumn($this);
		$column->name=$matches[1];
		if(isset($matches[3]) && $matches[3]!=='')
			$column->type=$matches[3];
		if(isset($matches[5]))
			$column->header=$matches[5];
		return $column;
	}

        public function registerClientScript()
        {
            parent::registerClientScript();
            $id=$this->getId();
            $ajaxUrl = Yii::app()->controller->createUrl($this->defaultController. '/'.$this->action);
            $cs=Yii::app()->getClientScript();
            $cs->registerScriptFile($this->ownScriptUri.'/editableGridView.js',CClientScript::POS_END);
            $cs->registerScript(__CLASS__.'#'.$this->grid_id,"jQuery('#$this->grid_id').editableGridView({url:'$ajaxUrl'});");
        }

        public function renderItems()
	{
		if($this->dataProvider->getItemCount()>0 || $this->showTableOnEmpty)
		{
                    $model = get_class($this->dataProvider->model);
			echo "<table class=\"{$this->itemsCssClass}\" id=\"{$this->grid_id}\" data-model='{$model}'>\n";
			$this->renderTableHeader();
			ob_start();
			$this->renderTableBody();
			$body=ob_get_clean();
			$this->renderTableFooter();
			echo $body; // TFOOT must appear before TBODY according to the standard.
			echo "</table>";
                        echo '<div id="editable-grid-modal"><div class="close"><span class="glyphicon glyphicon-remove-sign"></span></div><div class="msg-zone"></div></div>';
		}
		else
			$this->renderEmptyText();
	}
}

?>
