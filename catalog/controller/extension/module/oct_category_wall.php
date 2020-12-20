<?php
/**************************************************************/
/*	@copyright	OCTemplates 2018.							  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ControllerExtensionModuleOctCategoryWall extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/oct_category_wall');

        $data['heading_title'] = $setting['heading'][$this->session->data['language']];
        $data['text_see_more'] = $this->language->get('text_see_more');

        $data['position'] = $setting['position'];
        $data['limit']    = $setting['limit'];

        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $data['categories'] = array();

        if (isset($setting['module_categories']) && $setting['module_categories']) {

            foreach ($setting['module_categories'] as $category_id) {
                $category_info = $this->model_catalog_category->getCategory($category_id);

                if ($category_info) {
                    if ($category_info['image']) {
                        $category_image = $this->model_tool_image->resize($category_info['image'], $setting['width'], $setting['height']);
                    } else {
                        $category_image = $this->model_tool_image->resize('no-image.png', $setting['width'], $setting['height']);
                    }

                    $sub_categories = array();

                    if ($setting['show_sub_categories']) {
                        $category_children = $this->model_catalog_category->getCategories($category_id);

                        foreach ($category_children as $child) {
                            $sub_categories[] = array(
                                'name' => $child['name'],
                                'href' => $this->url->link('product/category', 'path=' . $category_id . '_' . $child['category_id'])
                            );
                        }
                    }

                    $cs_sort_order = array();

                    foreach ($sub_categories as $key => $value) {
                        $cs_sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($cs_sort_order, SORT_ASC, $sub_categories);

                    $data['categories'][] = array(
                        'category_id' => $category_info['category_id'],
                        'sort_order' => $category_info['sort_order'],
                        'thumb' => ($setting['show_image']) ? $category_image : false,
                        'name' => $category_info['name'],
                        'children' => $sub_categories,
                        'href' => $this->url->link('product/category', 'path=' . $category_info['category_id'])
                    );
                }
            }

            $c_sort_order = array();

            foreach ($data['categories'] as $key => $value) {
                $c_sort_order[$key] = $value['sort_order'];
            }

            array_multisort($c_sort_order, SORT_ASC, $data['categories']);

            return $this->load->view('extension/module/oct_category_wall', $data);
        }
    }
}
