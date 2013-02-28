(function ($) {

    function galleryManager(el, options) {
        //Defaults:
        this.defaults = {
            csrfToken: null,
            csrfTokenName: null,

            nameLabel: 'Name',
            descriptionLabel: 'Description',

            hasName: true,
            hasDesc: true,

            uploadUrl: '',
            deleteUrl: '',
            updateUrl: '',
            arrangeUrl: '',
            photos: []
        };
        //Extending options:
        var opts = $.extend({}, this.defaults, options);
        //code
        var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';

        var $gallery = $(el);
        if (!opts.hasName) {
            if (!opts.hasDesc) $gallery.addClass('no-name-no-desc');
            else $gallery.addClass('no-name');

        } else if (!opts.hasDesc)
            $gallery.addClass('no-desc');

        opts.wId = $gallery.attr('id');

        var $sorter = $('.sorter', $gallery);
        var $images = $('.images', $sorter);
        var $editorModal = $('.editor-modal', $gallery);
        var $progressOverlay = $('.progress-overlay', $gallery);
        var $uploadProgress = $('.upload-progress', $progressOverlay);
        var $editorForm = $('.form', $editorModal);

        function htmlEscape(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function createEditorElement(id, src, name, description) {
            var html = '<div class="photo-editor">' +
                '<div class="preview"><img src="' + htmlEscape(src) + '" alt=""/></div>' +
                '<div>' +
                (opts.hasName
                    ? '<label for="photo_name_' + id + '">' + opts.nameLabel + ':</label>' +
                    '<input type="text" name="photo[' + id + '][name]" class="input-xlarge" value="' + htmlEscape(name) + '" id="photo_name_' + id + '"/>'
                    : '') +
                (opts.hasDesc
                    ? '<label for="photo_description_' + id + '">' + opts.descriptionLabel + ':</label>' +
                    '<textarea name="photo[' + id + '][description]" rows="3" cols="40" class="input-xlarge" id="photo_description_' + id + '">' + htmlEscape(description) + '</textarea>'
                    : '') +
                '</div>' +
                '</div>';
            return $(html);
        }

        function createPhotoElement(id, src, name, description, rank) {
            var res = '<div id="' + opts.wId + '-' + id + '" class="photo">' +
                '<div class="image-preview"><img src="' + htmlEscape(src) + '"/></div><div class="caption">';
            if (opts.hasName)res += '<h5>' + htmlEscape(name) + '</h5>';
            if (opts.hasDesc)res += '<p>' + htmlEscape(description) + '</p>';
            res += '</div><input type="hidden" name="order[' + id + ']" value="' + rank + '"/><div class="actions">' +

                ((opts.hasName || opts.hasDesc)
                    ? '<span class="editPhoto btn btn-primary"><i class="icon-edit icon-white"></i></span> '
                    : '') +
                '<span class="deletePhoto btn btn-danger"><i class="icon-remove icon-white"></i></span>' +
                '</div><input type="checkbox" class="photo-select"/></div>';
            return $(res).data('id', id);
        }

        function deleteClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            // here can be question to confirm delete
            // if (!confirm(deleteConfirmation)) return false;
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl,
                data: 'id[]=' + id + csrfParams,
                success: function (t) {
                    if (t == 'OK') photo.remove();
                    else alert(t);
                }});
            return false;
        }

        function editClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            var src = $('img', photo).attr('src');
            var name = $('.caption h5', photo).text();
            var description = $('.caption p', photo).text();
            $editorForm.empty();
            $editorForm.append(createEditorElement(id, src, name, description));
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
                $this.closest('.photo').addClass('selected');
            else
                $this.closest('.photo').removeClass('selected');
            updateButtons();
        }

        $images
            .on('click', '.photo .deletePhoto', deleteClick)
            .on('click', '.photo .editPhoto', editClick)
            .on('click', '.photo .photo-select', selectChanged);


        $('.images', $sorter).sortable().disableSelection().bind("sortstop", function () {
            $.post(opts.arrangeUrl, $('input', $sorter).serialize() + csrfParams, function () {
                // order saved!
                // we can inform user that order saved
            }, 'json');
        });


        if (window.FormData !== undefined) { // if XHR2 available
            var uploadFileName = $('.afile', $gallery).attr('name');

            function multiUpload(files) {
                $progressOverlay.show();
                $uploadProgress.css('width', '5%');
                var filesCount = files.length;
                var uploadedCount = 0;
                $editorForm.empty();

                for (var i = 0; i < filesCount; i++) {
                    var fd = new FormData();

                    fd.append(uploadFileName, files[i]);
                    if (opts.csrfToken) {
                        fd.append(opts.csrfTokenName, opts.csrfToken);
                    }
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', opts.uploadUrl, true);
                    xhr.onload = function () {
                        uploadedCount++;
                        if (this.status == 200) {
                            var resp = JSON.parse(this.response);
                            var newOne = createPhotoElement(resp['id'], resp['preview'], resp['name'], resp['description'], resp['rank']);

                            $images.append(newOne);
                            if (opts.hasName || opts.hasDesc)
                                $editorForm.append(createEditorElement(resp['id'], resp['preview'], resp['name'], resp['description']));
                        }
                        $uploadProgress.css('width', '' + (5 + 95 * uploadedCount / filesCount) + '%');
                        console.log(uploadedCount);
                        if (uploadedCount === filesCount && (opts.hasName || opts.hasDesc)) {
                            $uploadProgress.css('width', '100%');
                            $progressOverlay.hide();
                            $editorModal.modal('show');
                        }
                    };
                    xhr.send(fd);
                }

            }

            (function () { // add drag and drop
                var el = $gallery[0];
                var isOver = false;
                var lastIsOver = false;

                setInterval(function () {
                    if (isOver != lastIsOver) {
                        if (isOver) el.classList.add('over');
                        else el.classList.remove('over');
                        lastIsOver = isOver
                    }
                }, 30);

                function handleDragOver(e) {
                    e.preventDefault();
                    isOver = true;
                    return false;
                }

                function handleDragLeave() {
                    isOver = false;
                    return false;
                }

                function handleDrop(e) {
                    e.preventDefault();
                    e.stopPropagation();


                    var files = e.dataTransfer.files;
                    multiUpload(files);

                    isOver = false;
                    return false;
                }

                function handleDragEnd() {
                    isOver = false;
                }


                el.addEventListener('dragover', handleDragOver, false);
                el.addEventListener('dragleave', handleDragLeave, false);
                el.addEventListener('drop', handleDrop, false);
                el.addEventListener('dragend', handleDragEnd, false);
            })();

            $('.afile', $gallery).attr('multiple', 'true').on('change', function (e) {
                e.preventDefault();
                multiUpload(this.files);
            });
        } else {
            $('.afile', $gallery).on('change', function (e) {
                e.preventDefault();
                $editorForm.empty();
                $progressOverlay.show();
                $uploadProgress.css('width', '5%');

                var data = {};
                if (opts.csrfToken)
                    data[opts.csrfTokenName] = opts.csrfToken;
                $.ajax({
                    type: 'POST',
                    url: opts.uploadUrl,
                    data: data,
                    files: $(this),
                    iframe: true,
                    processData: false,
                    dataType: "json"
                }).done(function (resp) {
                        var newOne = createPhotoElement(resp['id'], resp['preview'], resp['name'], resp['description'], resp['rank']);

                        $images.append(newOne);
                        if (opts.hasName || opts.hasDesc)
                            $editorForm.append(createEditorElement(resp['id'], resp['preview'], resp['name'], resp['description']));

                        $uploadProgress.css('width', '100%');
                        $progressOverlay.hide();
                        if (opts.hasName || opts.hasDesc) $editorModal.modal('show');
                    });


            });
        }

        $('.save-changes', $editorModal).click(function (e) {
            e.preventDefault();
            $.post(opts.updateUrl, $('input, textarea', $editorForm).serialize() + csrfParams, function (data) {
                var count = data.length;
                for (var key = 0; key < count; key++) {
                    var p = data[key];
                    var photo = $('#' + opts.wId + '-' + p.id);
                    $('img', photo).attr('src', p['src']);
                    if (opts.hasName)
                        $('.caption h5', photo).text(p['name']);
                    if (opts.hasDesc)
                        $('.caption p', photo).text(p['description']);
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

        $('.edit_selected', $gallery).click(function (e) {
            e.preventDefault();
            var cc = 0;
            var form = $editorForm.empty();
            $('.photo.selected', $sorter).each(function () {
                cc++;
                var photo = $(this),
                    id = photo.attr('id').substr((opts.wId + '-').length),
                    src = $('img', photo[0]).attr('src'),
                    name = $('.caption h5', photo[0]).text(),
                    description = $('.caption p', photo[0]).text();
                form.append(createEditorElement(id, src, name, description));
            });
            if (cc > 0)$editorModal.modal('show');
            return false;
        });

        $('.remove_selected', $gallery).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.photo.selected', $sorter).each(function () {
                ids.push($(this).attr('id').substr((opts.wId + '-').length));
            });
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl,
                data: 'id[]=' + ids.join('&id[]=') + csrfParams,
                success: function (t) {
                    if (t == 'OK') {
                        for (var i = 0, l = ids.length; i < l; i++) {
                            $('#' + opts.wId + '-' + ids[i]).remove();
                        }
                    } else alert(t);
                }});

        });

        $('.select_all', $gallery).change(function () {
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
        });

        for (var i = 0, l = opts.photos.length; i < l; i++) {
            var resp = opts.photos[i];
            var newOne = createPhotoElement(resp['id'], resp['preview'], resp['name'], resp['description'], resp['rank']).data('data', resp);
            $images.append(newOne);
        }
    }

    // The actual plugin
    $.fn.galleryManager = function (options) {
        if (this.length) {
            this.each(function () {
                galleryManager(this, options);
            });
        }
    };
})(jQuery);