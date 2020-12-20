<?php

class ControllerExtensionModuleIwatermark extends Controller {
    private $version = '3.2.2';
    private $mid = 'K4C4YXZ9K2';
    private $iid = '56';
    private $error;

    public function index() {
        $this->load->language('extension/module/iwatermark');

        $this->load->model('extension/module/iwatermark');

        $this->model_extension_module_iwatermark->linkFonts(DIR_SYSTEM . 'library/vendor/isenselabs/watermark/fonts', DIR_APPLICATION . 'view/stylesheet/vendor/isenselabs/watermark/fonts');
        
        $this->model_extension_module_iwatermark->updateEvents();

        $this->load->model('setting/setting');

        $stores = $this->model_extension_module_iwatermark->getStores();
        $store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : '0';

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            // Preserve license, if exists
            if ($store_id == 0) {
                $licensed_on = $this->model_setting_setting->getSettingValue('module_iwatermark_licensed_on');
                $license = @json_decode($this->model_setting_setting->getSettingValue('module_iwatermark_license'), true);

                if (!empty($licensed_on) && !empty($license)) {
                    $this->request->post['module_iwatermark_licensed_on'] = $licensed_on;
                    $this->request->post['module_iwatermark_license'] = $license;
                }
            }

            $this->model_extension_module_iwatermark->cleanNitroPackCache();

            $previous = $this->model_setting_setting->getSetting('module_iwatermark', $store_id);

            if (!empty($previous)) {
                $this->model_extension_module_iwatermark->persistSettingForCleaning($previous);
            }

            $this->model_extension_module_iwatermark->persistSettingForCleaning($this->request->post);

            $this->model_setting_setting->editSetting('module_iwatermark', $this->request->post, $store_id);

            $this->cache->delete('product');

            $success = $this->language->get('success_edit');
        } else if (isset($this->session->data['success'])) {
            $success = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $success = '';
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/vendor/isenselabs/watermark/dimension-container.js');
        $this->document->addScript('view/javascript/vendor/isenselabs/watermark/image-upload.js');
        $this->document->addScript('view/javascript/vendor/isenselabs/watermark/bootstrap-colorpicker.min.js');
        $this->document->addScript('view/javascript/vendor/isenselabs/watermark/bootstrap-select/bootstrap-select.js');
        $this->document->addScript('view/javascript/vendor/isenselabs/watermark/font-select.js');
        $this->document->addStyle('view/stylesheet/vendor/isenselabs/watermark/bootstrap-colorpicker.min.css');
        $this->document->addStyle('view/stylesheet/vendor/isenselabs/watermark/bootstrap-select.min.css');
        $this->document->addStyle('view/stylesheet/vendor/isenselabs/watermark/stylesheet.css');
        $this->document->addStyle(html_entity_decode($this->url->link('extension/module/iwatermark/fonts', 'user_token=' . $this->session->data['user_token'], true)));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'], true)
        );

        $this->model_extension_module_iwatermark->initSessionClean();

        $data['clean'] = !empty($this->session->data['iwatermark_clean']['settings']);

        $data['clean_url_work'] = html_entity_decode($this->url->link('extension/module/iwatermark/clean_work', 'user_token=' . $this->session->data['user_token'], true));
        $data['clean_url_cancel'] = html_entity_decode($this->url->link('extension/module/iwatermark/clean_cancel', 'user_token=' . $this->session->data['user_token'], true));

        $help = $this->url->link('extension/module/iwatermark/help', 'user_token=' . $this->session->data['user_token'], true);
        $data['help'] = $help;

        // Used only to check the license status, so it should be for store_id=0
        $setting = $this->model_setting_setting->getSetting('module_iwatermark');

        $data['error'] = '';

        if ($this->error) {
            $data['error'] = implode(' ', $this->error);
        } else if (empty($setting['module_iwatermark_licensed_on'])) {
            $data['error'] = sprintf($this->language->get('error_license_missing'), $help);
        }

        $data['success'] = $success;

        $data['heading_dashboard'] = $this->language->get('heading_title') . ' ' . $this->version;
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['save'] = $this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true);
        $data['upload_url'] = html_entity_decode($this->url->link('extension/module/iwatermark/upload', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true));
        $data['preview_url'] = html_entity_decode($this->url->link('extension/module/iwatermark/preview', 'user_token=' . $this->session->data['user_token'], true));

        $data['stores'] = $stores;
        $data['store'] = $stores[$store_id]['name'];

        $data['fonts'] = $this->model_extension_module_iwatermark->getFonts();

        $data['text_upload_info'] = sprintf($this->language->get('text_upload_info'), $this->model_extension_module_iwatermark->humanMaxUploadSize());
        $data['help_watermark_type'] = sprintf($this->language->get('help_watermark_type'), 'system/library/vendor/<br />isenselabs/watermark/fonts/');

        $data['user_token'] = $this->session->data['user_token'];

        $data['status'] = $this->getSettingValue('status');
        $data['dimension_type'] = $this->getSettingValue('dimension_type');
        $data['dimension_width'] = $this->getSettingValue('dimension_width');
        $data['dimension_height'] = $this->getSettingValue('dimension_height');
        $data['product_type'] = $this->getSettingValue('product_type');
        $data['products'] = $this->model_extension_module_iwatermark->populateProducts($this->getSettingValue('product'));
        $data['category_type'] = $this->getSettingValue('category_type');
        $data['categories'] = $this->model_extension_module_iwatermark->populateCategories($this->getSettingValue('category'));
        $data['watermark_type'] = $this->getSettingValue('watermark_type');
        $data['image'] = $this->model_extension_module_iwatermark->imageFromFile($this->getSettingValue('image_file'));
        $data['opacity_type'] = $this->getSettingValue('opacity_type');
        $data['opacity'] = (int)$this->getSettingValue('opacity', 70);
        $data['rotation'] = (int)$this->getSettingValue('rotation');
        $data['position'] = $this->getSettingValue('position', 'center');
        $data['text'] = $this->getSettingValue('text', 'DEMO');
        $data['font'] = $this->getSettingValue('font');
        $data['font_size'] = (int)$this->getSettingValue('font_size', 12);
        $data['color'] = $this->getSettingValue('color', '#ff0000');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/iwatermark/index', $data));
    }

    public function help() {
        $this->load->language('extension/module/iwatermark');

        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $license_settings = array();

            if (!empty($this->request->post['OaXRyb1BhY2sgLSBDb21'])) {
                $license_settings['module_iwatermark_licensed_on'] = $this->request->post['OaXRyb1BhY2sgLSBDb21'];
            }
                        
            if (!empty($this->request->post['cHRpbWl6YXRpb24ef4fe'])) {
                $license_settings['module_iwatermark_license'] = json_decode(base64_decode($this->request->post['cHRpbWl6YXRpb24ef4fe']), true);
            }

            $this->model_setting_setting->editSetting('module_iwatermark', array_merge($this->model_setting_setting->getSetting('module_iwatermark'), $license_settings));

            $this->session->data['success'] = $this->language->get('success_license');

            $this->response->redirect($this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('button_help'),
            'href' => $this->url->link('extension/module/iwatermark/help', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['error'] = '';

        if ($this->error) {
            $data['error'] = implode(' ', $this->error);
        }

        $data['cancel'] = $this->url->link('extension/module/iwatermark', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['license'] = $this->getSettingValue('license');

        $data['heading_help'] = sprintf($this->language->get('heading_help'), $this->language->get('heading_title') . ' ' . $this->version);
        
        $setting = $this->model_setting_setting->getSetting('module_iwatermark');

        $data['ticket_open'] = "http://isenselabs.com/tickets/open/" . base64_encode('Support Request') . '/' . base64_encode($this->iid) . '/' . base64_encode($this->request->server['SERVER_NAME']);

        if (!empty($setting['module_iwatermark_licensed_on']) && !empty($setting['module_iwatermark_license'])) {
            $data['licenced'] = true;
            $data['domains'] = $setting['module_iwatermark_license']['licenseDomainsUsed'];
            $data['customer'] = $setting['module_iwatermark_license']['customerName'];
            $data['license_encoded'] = base64_encode(json_encode($setting['module_iwatermark_license']));
            $data['license_expiry_date'] = date($this->language->get('date_format_short'), strtotime($setting['module_iwatermark_license']['licenseExpireDate']));
        } else {
            $data['licenced'] = false;
            $data['now'] = time();
            $data['mid'] = $this->mid;
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/iwatermark/help', $data));
    }

    public function upload() {
        $this->load->language('extension/module/iwatermark');

        $json = array();

        // Check user has permission
        if (!$this->validate()) {
            $json['error'] = $this->error['warning'];
        }

        if (!$json) {
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
                // Sanitize the filename
                $filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');

                if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types

                $filetypes = array('jpeg', 'jpg', 'png');
                
                if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $filetypes)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Allowed file mime types
                $mimes = array('image/jpeg', 'image/png');

                if (!in_array($this->request->files['file']['type'], $mimes)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $json['error'] = $this->language->get('error_upload_4');
            }
        }

        if (!$json) {
            $store_id = !empty($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;

            $file = 'isense_watermark_' . $store_id;

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

            $this->load->model('extension/module/iwatermark');

            $json['image'] = $this->model_extension_module_iwatermark->imageFromFile($file);

            $json['success'] = $this->language->get('success_upload');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function preview() {
        session_write_close();

        $this->load->model('extension/module/iwatermark');

        $settings = array(
            'type' => $this->request->post['module_iwatermark_watermark_type'],
            'font_size' => $this->request->post['module_iwatermark_font_size'],
            'font' => $this->request->post['module_iwatermark_font'],
            'text' => $this->request->post['module_iwatermark_text'],
            'color' => $this->request->post['module_iwatermark_color'],
            'image_file' => $this->request->post['module_iwatermark_image_file'],
            'opacity_type' => $this->request->post['module_iwatermark_opacity_type'],
            'opacity' => $this->request->post['module_iwatermark_opacity'],
            'rotation' => $this->request->post['module_iwatermark_rotation'],
            'position' => $this->request->post['module_iwatermark_position']
        );

        $filename = 'isense_watermark_preview.png';
        $source = DIR_APPLICATION . 'view/image/vendor/isenselabs/watermark/preview.png';
        $destination = DIR_UPLOAD . $filename;

        $image = new Image($source);
        $image->resize(300, 300);
        $image->iwatermark($this->registry, $settings);
        $image->save($destination);

        $json = $this->model_extension_module_iwatermark->imageFromFile($filename);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function image() {
        session_write_close();

        if (isset($this->request->get['file']) && file_exists(DIR_UPLOAD . $this->request->get['file'])) {
            $file = DIR_UPLOAD . $this->request->get['file'];

            $this->load->model('extension/module/iwatermark');

            $mime = $this->model_extension_module_iwatermark->getMime($file);

            if (!empty($mime)) {
                header('Content-Type: ' . $mime);
                header('Content-Length: ' . filesize($file));
                readfile($file);

                exit;
            }
        }

        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
        $this->response->setOutput('Not Found');
    }

    public function fonts() {
        session_write_close();
        
        $this->load->model('extension/module/iwatermark');

        $css = array();

        $data['fonts'] = $this->model_extension_module_iwatermark->getFonts();

        foreach ($data['fonts'] as $font) {
            $css[] = '@font-face { font-family: "' . addslashes($font['family_name']) . '"; src: url("view/stylesheet/vendor/isenselabs/watermark/fonts/' . $font['font_filename'] . '"); }';
        }

        $this->response->addHeader('Content-Type: text/css');
        $this->response->setOutput(implode(' ', $css));
    }

    public function clean_work() {
        $this->load->model('extension/module/iwatermark');
        
        $json = array();

        $json['done'] = false;

        if (is_null($this->session->data['iwatermark_clean']['progress'])) {
            $this->model_extension_module_iwatermark->cleanInitProgress();
        } else if (count($this->session->data['iwatermark_clean']['progress']) > 0) {
            $this->model_extension_module_iwatermark->cleanProceed();
        } else {
            $this->model_extension_module_iwatermark->cleanNitroPackCache();

            $this->model_extension_module_iwatermark->cleanFinalize();

            $json['done'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function clean_cancel() {
        // Only unset the session data. We need the temporary setting files so that the process may start over again on the next visit to the WaterMark admin panel.
        unset($this->session->data['iwatermark_clean']);
    }

    public function install() {
        if ($this->user->hasPermission('modify', 'extension/extension/module')) {
            $this->load->model('extension/module/iwatermark');

            $this->model_extension_module_iwatermark->addEvents();
        }
    }

    public function uninstall() {
        if ($this->user->hasPermission('modify', 'extension/extension/module')) {
            $this->load->model('extension/module/iwatermark');

            $this->model_extension_module_iwatermark->deleteEvents();

            $this->model_extension_module_iwatermark->unlinkFonts(DIR_APPLICATION . 'view/stylesheet/vendor/isenselabs/watermark/fonts');
        }
    }

    public function addProduct(&$route, &$args, &$output) {
        $this->load->model('extension/module/iwatermark');

        $this->model_extension_module_iwatermark->imageSymlinkUpdate($output);
    }

    public function editProduct(&$route, &$args, &$output) {
        $this->load->model('extension/module/iwatermark');

        $this->model_extension_module_iwatermark->imageSymlinkUpdate((int)$args[0]);
    }

    public function deleteProduct(&$route, &$args, &$output) {
        $this->load->model('extension/module/iwatermark');

        $this->model_extension_module_iwatermark->imageSymlinkDeleteProductDir((int)$args[0]);
    }

    protected function validate() {
        $this->error = array();

        if (!$this->user->hasPermission('modify', 'extension/module/iwatermark')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function getSettingValue($key, $default = null) {
        if (isset($this->request->post['module_iwatermark_' . $key])) {
            return $this->request->post['module_iwatermark_' . $key];
        } else {
            $store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : '0';

            $this->load->model('setting/setting');

            $settings = $this->model_setting_setting->getSetting('module_iwatermark', $store_id);

            if (isset($settings['module_iwatermark_' . $key])) {
                return $settings['module_iwatermark_' . $key];
            } else {
                return $default;
            }
        }
    }
}
