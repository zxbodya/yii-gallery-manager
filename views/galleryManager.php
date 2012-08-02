<?php
/**
 * @var $this GalleryManager
 * @var $model GalleryPhoto
 *
 * @author Bogdan Savluk <savluk.bogdan@gmail.com>
 */
?>
<?php
$cls = " ";
if (!($this->gallery->name)) $cls .= 'no-name';

if (!($this->gallery->description)) {
    $cls .= (($cls != ' ') ? '-' : '') . 'no-desc';
}

?>
<div class="GalleryEditor<?php echo $cls?>" id="<?php echo $this->id?>">
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
    <div class="sorter">
        <div class="images">
            <?php foreach ($this->gallery->galleryPhotos as $photo): ?>
            <div id="<?php echo $this->id . '-' . $photo->id ?>" class="photo">
                <div class="image-preview">
                    <?php echo CHtml::image($photo->getPreview()); ?>
                </div>
                <div class="caption">
                    <?php if ($this->gallery->name): ?>
                    <h5><?php echo $photo->name ?></h5>
                    <?php endif;?>
                    <?php if ($this->gallery->description): ?>
                    <p><?php echo $photo->description ?></p>
                    <?php endif;?>
                </div>
                <div class="actions">
                    <?php
                    echo CHtml::hiddenField('order[' . $photo->id . ']', $photo->rank);
                    if ($this->gallery->name || $this->gallery->description)
                        echo '<span data-photo-id="' . $photo->id . '" class="editPhoto btn btn-primary"><i class="icon-edit icon-white"></i></span>';
                    echo ' <span data-photo-id="' . $photo->id . '" class="deletePhoto btn btn-danger"><i class="icon-remove icon-white"></i></span>';
                    ?>
                </div>
                <label>
                    <input type="checkbox" class="photo-select"/>
                </label>
            </div>
            <?php endforeach;?>
        </div>
        <br style="clear: both;"/>
    </div>

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
</div>