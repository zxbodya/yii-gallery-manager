<?php
/**
 * Widget to manage gallery.
 * Requires Twitter Bootstrap styles to work.
 *
 * @author Bogdan Savluk <savluk.bogdan@gmail.com>
 */
class GalleryManager extends CWidget
{
    /** @var Gallery Model of gallery to manage */
    public $gallery;
    /** @var string Route to gallery controller */
    public $controllerRoute = false;
    public $assets;

    public function init()
    {
        $this->assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets');
    }

    /** Render widget */
    public function run()
    {

        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.min.js');

        if ($this->controllerRoute === null)
            throw new CException('$controllerRoute must be set.', 500);
        $model = new GalleryPhoto();
        $this->render('galleryManager', array(
            'model' => $model,
        ));
    }

}
