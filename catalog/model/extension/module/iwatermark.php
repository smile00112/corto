<?php

class ModelExtensionModuleIwatermark extends Model {
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

    public function imageSymlinkLinkImage($product_id, $original, $is_additional = false, $with_prefix = true) {
        return $this->loadImageSymlink()->linkImage($product_id, $original, $is_additional, $with_prefix);
    }

    public function isWatermarkable($file, $width, $height) {
        if (!$this->config->get('module_iwatermark_status')) {
            return false;
        }

        $regex = '~/\d+-\d+/(\d+)/(main|additional)/.*~i';
        $matches = array();

        if (!preg_match($regex, $file, $matches)) {
            return false;
        }

        $product_id = $matches[1];

        $meets_category_condition = true;
        $meets_product_condition = true;

        if ($this->config->get('module_iwatermark_category_type') == 'specific') {
            if (is_array($this->config->get('module_iwatermark_category'))) {
                $meets_category_condition = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product_to_category` WHERE product_id='" . (int)$product_id . "' AND category_id IN (" . implode(',', $this->config->get('module_iwatermark_category')) . ")")->num_rows > 0;
            } else {
                $meets_category_condition = false;
            }
        }

        if ($this->config->get('module_iwatermark_product_type') == 'specific') {
            if (is_array($this->config->get('module_iwatermark_product'))) {
                $meets_product_condition = in_array($product_id, $this->config->get('module_iwatermark_product'));
            } else {
                $meets_product_condition = false;
            }
        }

        if (!$meets_category_condition && !$meets_product_condition) {
            return false;
        }

        if ($this->config->get('module_iwatermark_dimension_type') == 'all' || 
            (
                $this->config->get('module_iwatermark_dimension_type') == 'bigger' && 
                (
                    $width > (int)$this->config->get('module_iwatermark_dimension_width') && !$this->config->get('module_iwatermark_dimension_height') || 
                    $height > (int)$this->config->get('module_iwatermark_dimension_height') && !$this->config->get('module_iwatermark_dimension_width') || 
                    $width > (int)$this->config->get('module_iwatermark_dimension_width') && $height > (int)$this->config->get('module_iwatermark_dimension_height')
                )
            )
            ||
            (
                $this->config->get('module_iwatermark_dimension_type') == 'smaller' && 
                (
                    $width < (int)$this->config->get('module_iwatermark_dimension_width') && !$this->config->get('module_iwatermark_dimension_height') || 
                    $height < (int)$this->config->get('module_iwatermark_dimension_height') && !$this->config->get('module_iwatermark_dimension_width') || 
                    $width < (int)$this->config->get('module_iwatermark_dimension_width') && $height < (int)$this->config->get('module_iwatermark_dimension_height')
                )
            )
        ) {
            return true;
        }

        return false;
    }

    public function getWatermarkSettings() {
        return array(
            'type' => $this->config->get('module_iwatermark_watermark_type'),
            'font_size' => $this->config->get('module_iwatermark_font_size'),
            'font' => $this->config->get('module_iwatermark_font'),
            'text' => $this->config->get('module_iwatermark_text'),
            'color' => $this->config->get('module_iwatermark_color'),
            'image_file' => $this->config->get('module_iwatermark_image_file'),
            'opacity_type' => $this->config->get('module_iwatermark_opacity_type'),
            'opacity' => $this->config->get('module_iwatermark_opacity'),
            'rotation' => $this->config->get('module_iwatermark_rotation'),
            'position' => $this->config->get('module_iwatermark_position')
        );
    }
}