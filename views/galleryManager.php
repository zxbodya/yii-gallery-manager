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
        <?php
        $form = $this->getController()->beginWidget('CActiveForm',
            array(
                'action' => Yii::app()->createUrl($this->controllerRoute . '/upload', array('gallery_id' => $this->gallery->id)),
                'method' => 'post',
                'htmlOptions' => array('enctype' => 'multipart/form-data'),
            ));
        /** @var CActiveForm $form */
        ?>
        <span class="btn btn-success fileinput-button">
                <i class="icon-plus icon-white"></i><span><?php echo Yii::t('galleryManager.main', 'Add images…');?></span>
            <?php echo $form->fileField($model, 'image', array('class' => 'afile', 'accept' => "image/*", 'multiple' => 'true'));?>
            </span>

        <span class="btn disabled edit_selected"><?php echo Yii::t('galleryManager.main', 'Edit selected');?></span>
        <span class="btn disabled remove_selected"><?php echo Yii::t('galleryManager.main', 'Remove selected');?></span>

        <label for="select_all_<?php echo $this->id?>" class="btn">
            <input type="checkbox"
                   id="select_all_<?php echo $this->id?>"
                   class="select_all"/> <?php echo Yii::t('galleryManager.main', 'Select all');?>
        </label>

        <!--  progress bar-->
        <!--<div style="display: inline-block; vertical-align: middle;">
            <div class="progress progress-success" style="width:200px; height: 20px; margin-bottom: 0;">
                <div class="bar" style="width: 40%;  height: 20px"></div>
            </div>
        </div>-->
        <?php
        echo CHtml::hiddenField('returnUrl', Yii::app()->getRequest()->getUrl() . '#' . $this->id);
        $this->getController()->endWidget();
        ?>
    </div>
    <hr/>
    <form
        method="post"
        action="<?php echo Yii::app()->createUrl($this->controllerRoute . '/order')?>"
        class="sorter"
        >
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
                <input type="checkbox" class="photo-select"/>
            </div>
            <?php endforeach;?>
        </div>
        <?php echo CHtml::hiddenField('returnUrl', Yii::app()->getRequest()->getUrl() . '#' . $this->id); ?>

        <br style="clear: both;"/>
    </form>

    <div class="modal hide editor-modal"> <!-- fade removed because of opera -->
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>

            <h3><?php echo Yii::t('galleryManager.main', 'Edit information')?></h3>
        </div>
        <div class="modal-body">
            <form action="<?php echo Yii::app()->createUrl($this->controllerRoute . '/changeData')?>"></form>
        </div>
        <div class="modal-footer">
            <a href="#"
               class="btn btn-primary save-changes"><?php echo Yii::t('galleryManager.main', 'Save changes')?></a>
            <a href="#" class="btn" data-dismiss="modal"><?php echo Yii::t('galleryManager.main', 'Close')?></a>
        </div>
    </div>
</div>

<?php
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');

$url = Yii::app()->createUrl($this->controllerRoute . '/delete', array('id' => ''));

$css = <<<EOD
    /* Photo Gallery */
    .GalleryEditor {
        border: 1px solid #DDD;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .GalleryEditor div.gform {
        padding-top: 4px;
        clear: left;
    }
    .GalleryEditor form{
        margin: 0;
    }

    .GalleryEditor .photo {
        position: relative;
        float: left;
        background-color: #fff;
        margin: 4px;
        height: 178px;
        width:140px;

        display: block;
        padding: 4px;
        line-height: 1;
        border: 1px solid #DDD;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .GalleryEditor .photo img {
        width: 140px;
        height: auto;
    }

    .GalleryEditor .photo a {
        padding-left: 8px;
    }

    .GalleryEditor .photo .actions {
        float: right;

        position: absolute;
        bottom: 4px;
        right: 4px;
    }


    .GalleryEditor hr{
        margin: 0 4px;
    }

    .GalleryEditor .fileinput-button {
        position: relative;
        overflow: hidden;
        margin-left: 8px;
        margin-top: 4px;
        margin-bottom: 4px;
    }
    .GalleryEditor .fileinput-button input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        border: solid transparent;
        border-width: 0 0 100px 200px;
        opacity: 0;
        filter: alpha(opacity=0);
        -moz-transform: translate(-300px, 0) scale(4);
        direction: ltr;
        cursor: pointer;
    }

    /* modal styles*/
    .GalleryEditor .preview {
        overflow: hidden;
        width: 200px;
        height: 156px;
        margin-right: 10px;
        overflow: hidden;
        float: left;
    }

    .GalleryEditor .preview img {
        width: 200px;
    }

    .GalleryEditor .photo-editor {
        min-height:156px;
        margin-bottom: 4px;
        padding: 4px;
        border: 1px solid #DDD;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .photo-editor form {
        margin-bottom: 0;
    }

    .GalleryEditor .caption p{
        height: 40px;
        overflow: hidden;
    }

    /* fixed thumbnail sizes */
    .GalleryEditor.no-desc .photo{
        height: 138px;
    }
    .GalleryEditor.no-name .photo{
        height: 160px;
    }
    .GalleryEditor.no-name-no-desc .photo{
        height: 120px;
    }
    .GalleryEditor .image-preview{
        height: 88px;
        overflow: hidden;
    }
    /* item selection */
    .GalleryEditor .photo-select{
        position: absolute;
        bottom: 8px;
        left: 8px;
    }
    .GalleryEditor .photo.selected{
        background-color: #cef;
        border-color: blue;
    }
EOD;

$cs->registerCss($this->id . 'css', $css);
?>
<script type="text/javascript">
$(function () {
    // variables from php
    var hasName = <?php echo $this->gallery->name ? 'true' : 'false' ?>;
    var hasDesc = <?php echo $this->gallery->description ? 'true' : 'false' ?>;

    var wId = '<?php echo $this->id?>';
    var ajaxUploadUrl = '<?php echo  Yii::app()->createUrl($this->controllerRoute . '/ajaxUpload', array('gallery_id' => $this->gallery->id))?>';
    var deleteUrl = '<?php echo  Yii::app()->createUrl($this->controllerRoute . '/delete')?>';

    var $gallery = $('#' + wId);
    var $sorter = $('.sorter', $gallery);
    var $images = $('.images', $sorter);
    var $editorModal = $('.editor-modal');
    var $editorForm = $('form', $editorModal);

    function photoEditorTemplate(id, src, name, description) {
        return '<div class="photo-editor">' +
            '<div class="preview"><img src="' + src + '" alt=""/></div>' +
            '<div>' +
            (hasName
                ? '<label for="photo_name_' + id + '"><?php echo Yii::t('galleryManager.main', 'Name');?>:</label>' +
                '<input type="text" name="photo[' + id + '][name]" class="input-xlarge" value="' + name + '" id="photo_name_' + id + '"/>'
                : '') +
            (hasDesc
                ? '<label for="photo_description_' + id + '"><?php echo Yii::t('galleryManager.main', 'Description');?>:</label>' +
                '<textarea name="photo[' + id + '][description]" rows="3" cols="40" class="input-xlarge" id="photo_description_' + id + '">' + description + '</textarea>'
                : '') +
            '</div>' +
            '</div>';
    }

    function photoTemplate(id, src, name, description, rank) {
        var res = '<div id="' + wId + '-' + id + '" class="photo">' +
            '<div class="image-preview"><img src="' + src + '"/></div><div class="caption">';
        if (hasName)res += '<h5>' + name + '</h5>';
        if (hasDesc)res += '<p>' + description + '</p>';
        res += '</div><input type="hidden" name="oreder[' + id + ']" value="' + rank + '"/><div class="actions">' +

            ((hasName || hasDesc)
                ? '<span data-photo-id="' + id + '" class="editPhoto btn btn-primary"><i class="icon-edit icon-white"></i></span> '
                : '') +
            '<span data-photo-id="' + id + '" class="deletePhoto btn btn-danger"><i class="icon-remove icon-white"></i></span>' +
            '</div><input type="checkbox" class="photo-select"/></div>';
        return res;
    }

    function deleteClick(e) {
        e.preventDefault();
        var id = $(this).data('photo-id');
        //if (!confirm(deleteConfirmation)) return false;
        $.ajax({
            type:'POST',
            url:deleteUrl,
            data:'id=' + id,
            success:function (t) {
                if (t == 'OK') $('#' + wId + '-' + id).remove();
                else alert(t);
            }});
        return false;
    }

    function editClick(e) {
        e.preventDefault();
        var id = $(this).data('photo-id');
        var photo = $(this).parents('.photo');
        var src = $('img', photo[0]).attr('src');
        var name = $('.caption h5', photo[0]).text();
        var description = $('.caption p', photo[0]).text();
        $editorForm.html(photoEditorTemplate(id, src, name, description));
        $editorModal.modal('show');
        return false;
    }

    function updateButtons() {
        var selectedCount = $('.photo.selected', $sorter).length;
        $('.select_all', $gallery).prop('checked', $('.photo', $sorter).length == selectedCount);
        if (selectedCount == 0) {
            $('.edit_selected, .remove_selected', $gallery).addClass('disabled');
        } else {
            $('.edit_selected, .remove_selected', $gallery).removeClass('disabled');
        }
    }

    function selectChanged() {
        var $this = $(this);
        if ($this.is(':checked'))
            $this.parent().addClass('selected');
        else
            $this.parent().removeClass('selected');
        updateButtons();
    }

    function bindPhotoEvents(newOne) {
        $('.deletePhoto', newOne).click(deleteClick);
        $('.editPhoto', newOne).click(editClick);
        $('.photo-select', newOne).change(selectChanged);
    }

    bindPhotoEvents($('.photo'));

    $('.images', $sorter).sortable().disableSelection().bind("sortstop", function (event, ui) {
        $.post($sorter.attr('action'), $sorter.serialize() + '&ajax=true', function (data, textStatus, jqXHR) {
            // order saved!
        }, 'json');
    });


    if (typeof window.FormData == 'function') {  // if XHR2 available
        $('.afile', $gallery).attr('multiple', 'true').on('change', function (e) {
            e.preventDefault();
            var filesCount = this.files.length;
            var uploadedCount = 0;
            $editorForm.html('');

            for (var i = 0; i < filesCount; i++) {
                var fd = new FormData();
                fd.append(this.name, this.files[i]);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxUploadUrl, true);
                xhr.onload = function () {
                    uploadedCount++;
                    if (this.status == 200) {
                        var resp = JSON.parse(this.response);
                        var newOne = $(photoTemplate(resp.id, resp.preview, resp.name, resp.description, resp.rank));

                        bindPhotoEvents(newOne);

                        $images.append(newOne);
                        if (hasName || hasDesc)
                            $editorForm.append($(photoEditorTemplate(resp.id, resp.preview, resp.name, resp.description)));
                    }
                    if (uploadedCount === filesCount && (hasName || hasDesc)) $editorModal.modal('show');
                };
                xhr.send(fd);
            }
        });
    } else {
        $('.afile', $gallery).on('change', function (e) {
            this.form.submit();
        });
    }

    $('.save-changes', $editorModal).click(function () {
        $.post($editorForm.attr('action'), $editorForm.serialize() + '&ajax=true', function (data, textStatus, jqXHR) {

            var count = data.length;
            for (var key = 0; key < count; key++) {
                var p = data[key];
                var photo = $('#' + wId + '-' + p.id);
                $('img', photo).attr('src', p.src);
                $('.caption h5', photo).text(p.name);
                $('.caption p', photo).text(p.description);
            }
            $editorModal.modal('hide');
            //deselect all items after editing
            $('.photo.selected', $sorter).each(function () {
                $('.photo-select', this).prop('checked', false)
            }).removeClass('selected');
            $('.select_all', $gallery).prop('checked', false);
            updateButtons();
        }, 'json');

    });

    $('.edit_selected').click(function (e) {
        e.preventDefault();
        var cc = 0;
        var form = $editorForm.html('');
        $('.photo.selected', $sorter).each(function () {
            cc++;
            var photo = $(this),
                id = photo.attr('id').substr((wId + '-').length),
                src = $('img', photo[0]).attr('src'),
                name = $('.caption h5', photo[0]).text(),
                description = $('.caption p', photo[0]).text();
            form.append(photoEditorTemplate(id, src, name, description));
        });
        if (cc > 0)$editorModal.modal('show');
        return false;
    });

    $('.remove_selected', $gallery).click(function () {
        $('.photo.selected', $sorter).each(function () {
            var id = $(this).attr('id').substr((wId + '-').length);
            $.ajax({
                type:'POST',
                url:deleteUrl,
                data:'id=' + id,
                success:function (t) {
                    if (t == 'OK') $('#' + wId + '-' + id).remove();
                    else alert(t);
                }});
        });
    });

    $('.select_all', $gallery).change(function (e) {
        if ($(this).prop('checked')) {
            $('.photo', $sorter).each(function () {
                $('.photo-select', this).prop('checked', true)
            }).addClass('selected');
        } else {
            $('.photo.selected', $sorter).each(function () {
                $('.photo-select', this).prop('checked', false)
            }).removeClass('selected');
        }
        updateButtons();
    }).parent().click(function (e) { //label event
            if ($(e.target).attr('id').substr(10) !== 'select_all' &&
                $(e.target).attr('for').substr(10) !== 'select_all')
                $('.select_all', $gallery).prop('checked', !$('.select_all', $gallery).prop('checked')).change();
        });

})
</script>