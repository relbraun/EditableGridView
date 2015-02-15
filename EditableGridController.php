<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditableGridController
 *
 * @author Kalman
 */
class EditableGridController extends CController
{

    private $successMsg = 'Updated';

    private $errorMsg;

    function actionAjaxUpdate()
        {
            if(isset($_POST['id'],$_POST['model'],$_POST[$_POST['model']])){
                $model=$this->loadModel($_POST['model'],$_POST['id']);
                $model->attributes=$_POST['Post'];
                if($model->save()){
                    $msg = array('status'=>1 , 'message' => $this->successMsg);
                }
                else{

                    $this->errorMsg =($model->errors);
                    $msg = array('status'=>0 , 'message' => $this->errorMsg);
                }

            }
            else{
                $this->errorMsg = 'you missing parameters.';
                $msg = array('status'=>0 , 'message' => $this->errorMsg);
            }
            header('Content-type:application/json');
             echo CJSON::encode($msg);
             Yii::app()->end();
        }

        protected function loadModel($model,$id)
        {
            $active=$model::model()->findByPk($id);
            if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $active;
        }
}

?>
