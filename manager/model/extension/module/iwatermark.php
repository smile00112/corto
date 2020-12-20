<?php

class ModelExtensionModuleIwatermark extends Model {
    private $events = array(
        'admin/model/catalog/product/addProduct/after' => 'extension/module/iwatermark/addProduct',
        'admin/model/catalog/product/editProduct/after' => 'extension/module/iwatermark/editProduct',
        'admin/model/catalog/product/deleteProduct/after' => 'extension/module/iwatermark/deleteProduct',
        'catalog/model/catalog/product/getProduct/after' => 'extension/module/iwatermark/getProduct',
        'catalog/model/catalog/product/getProducts/after' => 'extension/module/iwatermark/getProducts',
        'catalog/model/catalog/product/getProductSpecials/after' => 'extension/module/iwatermark/getProducts',
        'catalog/model/catalog/product/getLatestProducts/after' => 'extension/module/iwatermark/getProducts',
        'catalog/model/catalog/product/getPopularProducts/after' => 'extension/module/iwatermark/getProducts',
        'catalog/model/catalog/product/getBestSellerProducts/after' => 'extension/module/iwatermark/getProducts',
        'catalog/model/catalog/product/getProductRelated/after' => 'extension/module/iwatermark/getProducts',
        'catalog/model/catalog/product/getProductImages/after' => 'extension/module/iwatermark/getProductImages'
    );

    public function addEvents() {
        $this->load->model('setting/event');

        foreach ($this->events as $trigger => $action) {
            $this->model_setting_event->addEvent('iwatermark', $trigger, $action);
        }
    }

    public function deleteEvents() {
        $this->load->model('setting/event');
        
        $this->model_setting_event->deleteEventByCode('iwatermark');
    }

    public function updateEvents() {
        $this->load->model('setting/event');

        $this->model_setting_event->deleteEventByCode('watermark');
        $this->model_setting_event->deleteEventByCode('iwatermark');

        $this->addEvents();
    }

    public function getStores() {
        $result = array();

        $result['0'] = array(
            'name' => $this->config->get('config_name') . $this->language->get('text_default'),
            'url' => $this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'], true)
        );

        $this->load->model('setting/store');

        foreach ($this->model_setting_store->getStores() as $store) {
            $result[$store['store_id']] = array(
                'name' => $store['name'],
                'url' => $this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store['store_id'], true)
            );
        }

        return $result;
    }

    public function populateProducts($products) {
        if (empty($products)) {
            return array();
        }

        $result = array();

        $this->load->model('catalog/product');

        foreach ($products as $product_id) {
            $result[] = $this->model_catalog_product->getProduct($product_id);
        }

        return $result;
    }

    public function populateCategories($categories) {
        if (empty($categories)) {
            return array();
        }

        $result = array();

        $this->load->model('catalog/category');

        foreach ($categories as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);

            $path = $category_info['path'] ? $category_info['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' : '';

            $category_info['name'] = $path . $category_info['name'];

            $result[] = $category_info;
        }

        return $result;
    }

    public function humanMaxUploadSize() {
        $upload = $this->toBytes(trim(ini_get('upload_max_filesize')));
        $post = $this->toBytes(trim(ini_get('post_max_size'))) - 524288;
        
        $bytes = min($upload, $post);

        return $this->sizeToString($bytes);
    }
    
    public function imageFromFile($file) {
        if (!is_file(DIR_UPLOAD . $file)) {
            return null;
        }

        return array(
            'file' => $file,
            'url' => html_entity_decode($this->url->link('extension/module/iwatermark/image', 'user_token=' . $this->session->data['user_token'] . '&file=' . urlencode($file) . '&hash=' . md5_file(DIR_UPLOAD . $file), true))
        );
    }

    public function getFonts() {
        $dir = DIR_SYSTEM . 'library/vendor/isenselabs/watermark/fonts/';

        $installed_fonts = array_map(
            array($this, 'fontEntity'), 
            array_values(
                array_filter(
                    scandir($dir), 
                    array($this, 'filterFonts')
                )
            )
        );

        return $installed_fonts;
    }

    public function loadImageSymlink() {
        if (!$this->registry->has('watermark_image_symlink')) {
            $file = DIR_SYSTEM . 'library/vendor/isenselabs/watermark/OpenCartImageSymlink.php';

            if (class_exists('\\VQMod')) {
                include_once(\VQMod::modCheck(modification($file)));
            } else {
                include_once(modification($file));
            }

            $this->registry->set('watermark_image_symlink', new OpenCartImageSymlink($this->registry));
        }

        return $this->registry->get('watermark_image_symlink');
    }

    public function imageSymlinkUpdate($product_id) {
        return $this->loadImageSymlink()->update($product_id);
    }

    public function imageSymlinkDeleteProductDir($product_id) {
        return $this->loadImageSymlink()->deleteProductDir($product_id);
    }

    public function linkFonts($original_path, $target_path) {
        // If the link does not exist, create it. Otherwise, just return it.
        if (!file_exists($target_path)) {
            if (false === @symlink($original_path, $target_path) && false === $this->copyDir($original_path, $target_path)) {
                if ($this->config->get('config_error_log')) {
                    $this->log->write("[WaterMark admin panel]: Could not create symlink/copy of " . $original_path . " to: " . $target_path);
                }
            } else {
                return $target_path;
            }
        } else {
            return $target_path;
        }

        return false;
    }

    public function unlinkFonts($target_path) {
        if (is_link($target_path)) {
            return @unlink($target_path);
        } else if (is_dir($target_path)) {
            $this->iterateDir($target_path, function($item) {
                if (is_file($item) && is_writable($item)) {
                    @unlink($item);
                }
            });
        }
    }

    private function copyDir($original_path, $target_path) {
        $result = true;

        if (!is_dir($target_path)) {
            @mkdir($target_path, 0755, true);
        }

        $this->iterateDir($original_path, function($source) use (&$result, &$target_path) {
            if (is_file($source) && is_readable($source)) {
                $destination = $target_path . '/' . basename($source);

                if ((!file_exists($destination) || filesize($destination) != filesize($source)) && is_writable($target_path)) {
                    $result = $result && copy($source, $destination);
                } else if (!is_writable($target_path)) {
                    $result = false;
                }
            }
        });

        return $result;
    }

    private function iterateDir($dir, $callback) {
        clearstatcache(true);

        $dh = opendir($dir);

        while (false !== ($entry = readdir($dh))) {
            if (in_array($entry, array('.', '..', 'index.html'))) {
                continue;
            }

            $item = $dir . DIRECTORY_SEPARATOR . $entry;

            $callback($item);
        }

        closedir($dh);
    }

    private function iterateCleanDir($callback) {
        $dir = DIR_SYSTEM . 'library/vendor/isenselabs/watermark/clean/';

        $this->iterateDir($dir, $callback);
    }

    public function initSessionClean() {
        // Read all persisted settings and populate the session if necessary.
        if (!empty($this->session->data['iwatermark_clean'])) {
            unset($this->session->data['iwatermark_clean']);
        }

        $this->session->data['iwatermark_clean']['settings'] = array();
        $this->session->data['iwatermark_clean']['progress'] = null;

        $this->iterateCleanDir(function($item) {
            if (is_file($item) && is_readable($item)) {
                $data = json_decode(file_get_contents($item), true);

                $this->session->data['iwatermark_clean']['settings'][] = $data;
            }
        });

    }

    public function cleanFinalize() {
        // Unlink all persisted settings because we are sure the cache cleaning is finished.
        $this->iterateCleanDir(function($item) {
            if (is_file($item) && is_writable($item)) {
                @unlink($item);
            }
        });

        unset($this->session->data['iwatermark_clean']);
    }

    public function persistSettingForCleaning($setting) {
        // The passed setting is persisted in a file in case the customer stop the process in the middle. This way we can stack many different setting histories and clean all relevant image/cache files only once if necessary.
        $dir = DIR_SYSTEM . 'library/vendor/isenselabs/watermark/clean/';

        $data = json_encode($setting);

        $filename = md5($data);

        @file_put_contents($dir . '/' . $filename, $data);
    }

    public function cleanInitProgress() {
        // Initialize the cleaning progress. Fill the session variable $this->session->data['iwatermark_clean']['progress'] with all existing product groups.

        $result = array();

        if (false !== $dir = $this->loadImageSymlink()->getGroupDir()) {
            $regex = '~(\d+-\d+)~i';

            clearstatcache(true);

            $dh = opendir($dir);
            
            $dir_image = realpath(DIR_IMAGE);

            while (false !== ($entry = readdir($dh))) {
                if (in_array($entry, array('.', '..'))) {
                    continue;
                }

                $real_item = $dir . DIRECTORY_SEPARATOR . $entry;
                $item = $dir_image . DIRECTORY_SEPARATOR . 'cache' . substr($real_item, strlen($dir_image));

                if (preg_match($regex, $entry) && is_dir($item) && is_writable($item)) {
                    $result[] = $item;
                }
            }

            closedir($dh);
        }

        $this->session->data['iwatermark_clean']['progress'] = $result;
    }

    public function cleanProceed() {
        // Proceed with this step of the cleaning. Get the last item in $this->session->data['iwatermark_clean']['progress'], which is a product_id group in the form 1-1000. Find every product ID in this directory and clean its cache according to the settings.

        $progress = $this->session->data['iwatermark_clean']['progress'];

        $dir = $progress[count($progress) - 1];

        clearstatcache(true);

        $dh = opendir($dir);

        while (false !== ($entry = readdir($dh))) {
            if (in_array($entry, array('.', '..'))) {
                continue;
            }

            $item = $dir . DIRECTORY_SEPARATOR . $entry;

            if (is_numeric($entry) && $this->passesAnyProductIdCondition($entry)) {
                $this->cleanImagesByDimensions($item . DIRECTORY_SEPARATOR . 'main');
                $this->cleanImagesByDimensions($item . DIRECTORY_SEPARATOR . 'additional');
            }
        }

        closedir($dh);

        array_pop($this->session->data['iwatermark_clean']['progress']);
    }

    public function cleanNitroPackCache() {
        if (file_exists(DIR_SYSTEM . 'library/vendor/isenselabs/nitropack/config.php')) {
            $this->load->model('extension/module/nitro');

            if (function_exists('truncateNitroProductCache') && function_exists('getQuickCacheRefreshFilename')) {
                truncateNitroProductCache();
                $nitro_filename = getQuickCacheRefreshFilename();
                touch($nitro_filename);
            }
        }
    }

    public function getMime($file) {
        if (function_exists('mime_content_type')) {
            return mime_content_type($file);
        } else {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            switch ($ext) {
                case 'png' : return 'image/png';
                case 'jpg' :
                case 'jpeg' : return 'image/jpeg';
                case 'gif' : return 'image/gif';
            }
        }
    }

    private function cleanImagesByDimensions($dir) {
        // Iterate through all images in the $dir directory, extract their dimensions, and delete them if they match any of the settings.

        if (!is_dir($dir) || !is_writable($dir)) return;

        clearstatcache(true);

        $dh = opendir($dir);
        $regex = '~(\d+)x(\d+)~i';

        while (false !== ($entry = readdir($dh))) {
            if (in_array($entry, array('.', '..'))) {
                continue;
            }

            $item = $dir . DIRECTORY_SEPARATOR . $entry;
            $matches = array();

            if (preg_match($regex, $entry, $matches)) {
                $width = $matches[1];
                $height = $matches[2];

                foreach ($this->session->data['iwatermark_clean']['settings'] as $setting) {
                    if ($this->passesWidthHeightConditions($width, $height, $setting)) {
                        clearstatcache(true);

                        if (is_file($item) && is_writable($item)) {
                            @unlink($item);
                        }
                    }
                }
            }
        }

        closedir($dh);
    }

    private function passesWidthHeightConditions($width, $height, $setting) {
        // Check if these settings match the provided width and height

        return 
            !isset($setting['module_iwatermark_dimension_type']) ||
            $setting['module_iwatermark_dimension_type'] == 'all' || 
            (
                $setting['module_iwatermark_dimension_type'] == 'bigger' && 
                (
                    $width > (int)$setting['module_iwatermark_dimension_width'] && !$setting['module_iwatermark_dimension_height'] || 
                    $height > (int)$setting['module_iwatermark_dimension_height'] && !$setting['module_iwatermark_dimension_width'] || 
                    $width > (int)$setting['module_iwatermark_dimension_width'] && $height > (int)$setting['module_iwatermark_dimension_height']
                )
            )
            ||
            (
                $setting['module_iwatermark_dimension_type'] == 'smaller' && 
                (
                    $width < (int)$setting['module_iwatermark_dimension_width'] && !$setting['module_iwatermark_dimension_height'] || 
                    $height < (int)$setting['module_iwatermark_dimension_height'] && !$setting['module_iwatermark_dimension_width'] || 
                    $width < (int)$setting['module_iwatermark_dimension_width'] && $height < (int)$setting['module_iwatermark_dimension_height']
                )
            );
    }

    private function passesAnyProductIdCondition($product_id) {
        // Iterate through all settings and see if any of them match this product_id

        $result = false;

        foreach ($this->session->data['iwatermark_clean']['settings'] as $setting) {
            if (!$result) {
                $result = $result || $this->passesProductIdConditions($product_id, $setting);
            }
        }

        return $result;
    }

    private function passesProductIdConditions($product_id, $setting) {
        // Check if settings match this specific product ID

        $meets_category_condition = true;
        $meets_product_condition = true;

        if (isset($setting['module_iwatermark_category_type']) && $setting['module_iwatermark_category_type'] == 'specific') {
            if (isset($setting['module_iwatermark_category']) && is_array($setting['module_iwatermark_category'])) {
                $meets_category_condition = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product_to_category` WHERE product_id='" . (int)$product_id . "' AND category_id IN (" . implode(',', $setting['module_iwatermark_category']) . ")")->num_rows > 0;
            } else {
                $meets_category_condition = false;
            }
        }

        if (isset($setting['module_iwatermark_product_type']) && $setting['module_iwatermark_product_type'] == 'specific') {
            if (isset($setting['module_iwatermark_product']) && is_array($setting['module_iwatermark_product'])) {
                $meets_product_condition = in_array($product_id, $setting['module_iwatermark_product']);
            } else {
                $meets_product_condition = false;
            }
        }

        return $meets_category_condition || $meets_product_condition;
    }

    private function fontEntity($ttf) {
        return array(
            'family_name' => trim(preg_replace('~\.ttf$~i', '', $ttf)),
            'font_filename' => $ttf
        );
    }

    private function filterFonts($item) {
        return preg_match('~\.ttf$~i', $item);
    }

    // Based on http://php.net/manual/en/function.ini-get.php
    private function toBytes($configValue) {
        $last = strtolower($configValue[strlen($configValue) - 1]);

        $number = preg_replace('~[^0-9]~', '', $configValue);

        switch($last) {
            case 'g':
                $number *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $number *= 1024 * 1024;
                break;
            case 'k':
                $number *= 1024;
                break;
        }

        return $number;
    }

    private function sizeToString($size) {
        $count = 0;

        for ($i = $size; $i >= 1024; $i /= 1024) {
            $count++;
        }

        switch ($count) {
            case 0 : $suffix = ' B'; break;
            case 1 : $suffix = ' KB'; break;
            case 2 : $suffix = ' MB'; break;
            case 3 : $suffix = ' GB'; break;
            default : $suffix = ' TB'; break;
        }

        return round($i, 2) . $suffix;
    }
}