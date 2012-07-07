Gallery Manager usage instructions
===========================

Manual
------

1. Checkout source code to your project, for example to ext.galleryManager.
2. Install and configure image component(https://bitbucket.org/z_bodya/yii-image).
3. Import gallery models to project, by adding "ext.galleryManager.models.*" to import in config/main.php
4. Add GalleryController to application or module controllerMap.
5. Configure and save gallery model

        Example:
        $gallery = new Gallery();
        $gallery->name = true;
        $gallery->description = true;
        $gallery->versions = array(
            'small' => array(
                'resize' => array(200, null),
            ),
            'medium' => array(
                'resize' => array(800, null),
            )
        );
        $gallery->save();

6. Render widget for gallery created above:

        $this->widget('GalleryManager', array(
            'gallery' => $gallery,
            'controllerRoute' => '/admin/gallery', //route to gallery controller
        ));

Using GalleryBehavior
----------------------
Using gallery behavior is possible to add gallery to any model in application.

GalleryBehavior is under development and will be finished soon, so usage examples also will be later.