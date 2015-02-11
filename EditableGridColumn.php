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
}

?>
