<?php
/**
 * @var $this GalleryManager
 * @var $model GalleryPhoto
 *
 * @author Bogdan Savluk <savluk.bogdan@gmail.com>
 */
?>
<?php echo CHtml::openTag('div', $this->htmlOptions); ?>
<!-- Gallery Toolbar -->
<div class="gform">
        <span class="btn btn-success fileinput-button">
            <i class="icon-plus icon-white"></i>
            <?php echo Yii::t('galleryManager.main', 'Add images…');?>
            <?php echo CHtml::activeFileField($model, 'image', array('class' => 'afile', 'accept' => "image/*", 'multiple' => 'true'));?>
        </span>

    <span class="btn disabled edit_selected"><?php echo Yii::t('galleryManager.main', 'Edit selected');?></span>
    <span class="btn disabled remove_selected"><?php echo Yii::t('galleryManager.main', 'Remove selected');?></span>

    <label for="select_all_<?php echo $this->id?>" class="btn">
        <input type="checkbox" style="margin-bottom: 4px;"
               id="select_all_<?php echo $this->id?>"
               class="select_all"/>
        <?php echo Yii::t('galleryManager.main', 'Select all');?>
    </label>

    <!--  progress bar-->
    <!--<div style="display: inline-block; vertical-align: middle;">
        <div class="progress progress-success" style="width:200px; height: 20px; margin-bottom: 0;">
            <div class="bar" style="width: 40%;  height: 20px"></div>
        </div>
    </div>-->
    <?php
    echo CHtml::hiddenField('returnUrl', Yii::app()->getRequest()->getUrl() . '#' . $this->id);
    ?>
</div>
<hr/>
<!-- Gallery Photos -->
<div class="sorter">
    <div class="images"></div>
    <br style="clear: both;"/>
</div>
<!-- Modal window to edit photo information -->
<div class="modal hide editor-modal"> <!-- fade removed because of opera -->
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>

        <h3><?php echo Yii::t('galleryManager.main', 'Edit information')?></h3>
    </div>
    <div class="modal-body">
        <div class="form"></div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary save-changes">
            <?php echo Yii::t('galleryManager.main', 'Save changes')?>
        </a>
        <a href="#" class="btn" data-dismiss="modal"><?php echo Yii::t('galleryManager.main', 'Close')?></a>
    </div>
</div>
<?php echo CHtml::closeTag('div'); ?>