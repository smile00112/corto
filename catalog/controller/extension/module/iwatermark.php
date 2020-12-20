<?php

class ControllerExtensionModuleIwatermark extends Controller {
    public function getProduct(&$route, &$args, &$output) {
        if (!empty($output['product_id']) && !empty($output['image']) && $this->config->get('module_iwatermark_status')) {
            $this->load->model('extension/module/iwatermark');

            $output['image'] = $this->model_extension_module_iwatermark->imageSymlinkLinkImage($output['product_id'], $output['image'], false, false);
        }
    }

    public function getProducts(&$route, &$args, &$output) {
        if ($this->config->get('module_iwatermark_status')) {
            $this->load->model('extension/module/iwatermark');

            foreach ($output as &$product) {
                if (!empty($product['product_id']) && !empty($product['image'])) {
                    if($product['image'] != 'catalog/noimage.png')
                        $product['image'] = $this->model_extension_module_iwatermark->imageSymlinkLinkImage($product['product_id'], $product['image'], false, false);
                }
            }
        }
    }

    public function getProductImages(&$route, &$args, &$output) {
        if ($this->config->get('module_iwatermark_status')) {
            $this->load->model('extension/module/iwatermark');
            
            foreach ($output as &$product_image) {
                if (!empty($product_image['product_id']) && !empty($product_image['image'])) {
                    $product_image['image'] = $this->model_extension_module_iwatermark->imageSymlinkLinkImage($product_image['product_id'], $product_image['image'], true, false);
                }
            }
        }
    }
}