<?php

Yii::import('zii.widgets.grid.CGridView');
Yii::import('ext.EditableGridView.EditableGridColumn');

/**
 * Description of EditableGridView
 *
 * @author Kalman
 */
class EditableGridView extends CGridView
{

    public $ownScriptUri;

    private $grid_id;

    public function init()
    {

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
            //$this->grid_id = get_class($this->dataProvider->data[0]) . '-editable-grid';
            $cs=Yii::app()->getClientScript();
            $cs->registerScriptFile($this->ownScriptUri.'/editableGridView.js',CClientScript::POS_END);
            $cs->registerScript(__CLASS__.'#'.$this->grid_id,"jQuery('#$this->grid_id').editableGridView({url:'url'});");
        }

        public function renderItems()
	{
		if($this->dataProvider->getItemCount()>0 || $this->showTableOnEmpty)
		{
			echo "<table class=\"{$this->itemsCssClass}\" id=\"{$this->grid_id}\">\n";
			$this->renderTableHeader();
			ob_start();
			$this->renderTableBody();
			$body=ob_get_clean();
			$this->renderTableFooter();
			echo $body; // TFOOT must appear before TBODY according to the standard.
			echo "</table>";
		}
		else
			$this->renderEmptyText();
	}
}

?>
