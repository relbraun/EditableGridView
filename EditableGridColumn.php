<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditableGridColumn
 *
 * @author Kalman
 */
class EditableGridColumn extends CDataColumn
{

    public $field_type = 'text';

    public $dropDownData = array();


        protected function renderDataCellContent($row,$data)
	{
            $id=$data->primaryKey;
		if($this->value!==null)
			$value=$this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
		else if($this->name!==null)
			$value=CHtml::value($data,$this->name);
                    switch ($this->field_type) {
                        case 'text':
                            $input=CHtml::activeTextField($data, $this->name, array('data-id'=>$data->primaryKey));
                            break;
                        case 'dropdown':
                            $input=CHtml::activeDropDownList($data, $this->name, $this->dropDownData, array('data-id'=>$data->primaryKey));
                            break;
                        case 'checkbox':
                            $input=CHtml::activeCheckBox($data, $this->name, $this->dropDownData, array('data-id'=>$data->primaryKey));
                            break;
                        default:
                            break;
                    }
		echo $value===null ? $this->grid->nullDisplay : $this->grid->getFormatter()->format($value,$this->type);
	}

        public function renderDataCell($row)
	{
		$data=$this->grid->dataProvider->data[$row];
		$options=$this->htmlOptions;
		if($this->cssClassExpression!==null)
		{
			$class=$this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
			if(!empty($class))
			{
				if(isset($options['class']))
					$options['class'].=' '.$class;
				else
					$options['class']=$class;
			}
		}
                $isEditable=!$data->{$this->name}->metaData->columns[$this->name]->autoIncrement;
                if($isEditable){
                    $options['class'].=' '.'editable';
                }
		echo CHtml::openTag('td',$options);
		$this->renderDataCellContent($row,$data);
		echo '</td>';
	}
}

?>
