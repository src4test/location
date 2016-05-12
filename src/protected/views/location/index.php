<?php
/* @var $this LocationController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Locations',
);

?>

<h1>Locations</h1>

<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,

    'columns'=>array(
        'id',
        'title',
        'latitude',
        'longitude',

        array(
            'name'=>'icon',
            'type'=>'html',
            'value'=>'(!empty($data->icon))?CHtml::image("'.$this->assetsBase."/icons/".'$data->icon","",array("style"=>"width:25px;height:25px;")):"no image"'
        ),

        array(
            'name'=>'create_time',
            'value' => '$data->create_time!==null ? Yii::app()->dateFormatter->format("d MMM yyyy, HH:mm", $data->create_time) : ""',
        ),
        array(
            'name'=>'update_time',
            'value' => '$data->update_time!==null ? Yii::app()->dateFormatter->format("d MMM yyyy, HH:mm", $data->update_time) : ""',
        ),
        array(
            'class'=>'CButtonColumn', 
        ),
    ),
)); 

$markers = $dataProvider->getData(); 

?>

<?php
if(count($markers) > 0):
?>
    <div id="mapCanvas"></div>
<?php

    Yii::app()->clientScript->registerScriptFile('http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize', CClientScript::POS_END);

    $markersJs = '';
    $infoWindowJs = '';
    $iconsJs = '';
    foreach($markers as $marker):               
       $markersJs .= '[' . $marker->latitude . ',' . $marker->longitude . '],';
       $infoWindowJs .= '[\'' . preg_replace( "/\r|\n/", "", str_replace('\'', '&#39;', $marker->title)) . '\'],';
       $iconsJs .= '[\'' . $marker->icon . '\'],';
    endforeach;
                          
    Yii::app()->clientScript->registerScript('mapjs','
        var map;
        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: \'roadmap\'
        };

        map = new google.maps.Map(document.getElementById("mapCanvas"), mapOptions);
        map.setTilt(45);
            
        var markers = [' . $markersJs . '];
        var infoWindowContent = [' . $infoWindowJs . '];
        var icons = [' . $iconsJs . '];
        
        var infoWindow = new google.maps.InfoWindow(), marker, i;
        
        for( i = 0; i < markers.length; i++ ) {
            var position = new google.maps.LatLng(markers[i][0], markers[i][1]);
            bounds.extend(position);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: infoWindowContent[i][0],
                icon: \'' . $this->assetsBase . '/icons/\'' . '+ icons[i]
            });
            
            google.maps.event.addListener(marker, \'click\', (function(marker, i) {
                return function() {
                    infoWindow.setContent(infoWindowContent[i][0]);
                    infoWindow.open(map, marker);
                }
            })(marker, i));
    
            map.fitBounds(bounds);
        }
    
        var boundsListener = google.maps.event.addListener((map), \'bounds_changed\', function(event) {
            ' . 
            (count($markers) == 1 ? 'this.setZoom(17)' : '')
           . '
            google.maps.event.removeListener(boundsListener);
        });
    ');
endif;
?>
