<?php
/* @var $this LocationController */
/* @var $model Location */
/* @var $form CActiveForm */
Yii::app()->clientScript->registerScriptFile('https://maps.googleapis.com/maps/api/js?sensor=false', CClientScript::POS_BEGIN);
Yii::app()->getClientScript()->registerCoreScript('jquery');

Yii::app()->clientScript->registerScript('markerjs','
        $("#Location_icon").change(function(){
           $("#icon-preview").attr("src","' . $this->assetsBase . '/icons/' . '" + $("#Location_icon").val());
        });
        
        $(document).ready(function(){
           $("#icon-preview").attr("src","' . $this->assetsBase . '/icons/' . '" + $("#Location_icon").val()); 
        });
    ');


?>

<script type="text/javascript">

function updateMarkerPosition(latLng){
	document.getElementById('Location_latitude').value = latLng.lat();
	document.getElementById('Location_longitude').value = latLng.lng();
}

function initialize() {
  var latLng = new google.maps.LatLng(50.449578, 30.523222);
  var map = new google.maps.Map(document.getElementById('mapCanvas'), {
    zoom: 8,
    center: latLng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });
  var marker = new google.maps.Marker({
    position: latLng,
    title: 'Укажите местоположение',
    map: map,
    draggable: true
  });

  updateMarkerPosition(latLng);

  google.maps.event.addListener(marker, 'drag', function() {
  	updateMarkerPosition(marker.getPosition())
  });

}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'location-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'icon'); ?>
        <?php echo $form->listBox($model,'icon', $icons, array('class' => 'width-200 height-200')); ?>
        <?php echo $form->error($model,'icon'); ?>
        <img id="icon-preview" src="" />  
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'latitude'); ?>
		<?php echo $form->textField($model,'latitude', array('readonly'=>true)); ?>
		<?php echo $form->error($model,'latitude'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'longitude'); ?>
		<?php echo $form->textField($model,'longitude', array('readonly'=>true)); ?>
		<?php echo $form->error($model,'longitude'); ?>
        <div id="mapCanvas"></div>
     </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->