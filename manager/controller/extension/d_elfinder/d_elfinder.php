<?php
class ControllerExtensionDElfinderDElfinder extends Controller {
    private $error = array();

    public function connector()
    {
        require_once(DIR_SYSTEM . 'library/d_elfinder/connector.php');

        header('Access-Control-Allow-Origin: *');
        $connector = new elFinderConnector(new elFinder($opts), true);
        $connector->run();
    }

}
