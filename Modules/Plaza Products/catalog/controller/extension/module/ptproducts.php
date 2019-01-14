<?php
class ControllerExtensionModulePtproducts extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/ptproducts');

        $this->load->model('catalog/product');
        $this->load->model('plaza/catalog');
        $this->load->model('tool/image');
        $this->load->model('plaza/rotateimage');

        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
        $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
        $this->document->addScript('catalog/view/javascript/plaza/countdown/countdown.js');
        if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/plaza/module.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/plaza/module.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/plaza/module.css');
        }

        $data = array();

        $data['module_id'] = $setting['module_id'];

        /* Catalog Settings */
        $store_id = $this->config->get('config_store_id');

        if(isset($this->config->get('module_ptcontrolpanel_module_price')[$store_id])) {
            $data['show_module_price'] = (int) $this->config->get('module_ptcontrolpanel_module_price')[$store_id];
        } else {
            $data['show_module_price'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_cart')[$store_id])) {
            $data['show_module_cart'] = (int) $this->config->get('module_ptcontrolpanel_module_cart')[$store_id];
        } else {
            $data['show_module_cart'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_wishlist')[$store_id])) {
            $data['show_module_wishlist'] = (int) $this->config->get('module_ptcontrolpanel_module_wishlist')[$store_id];
        } else {
            $data['show_module_wishlist'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_compare')[$store_id])) {
            $data['show_module_compare'] = (int) $this->config->get('module_ptcontrolpanel_module_compare')[$store_id];
        } else {
            $data['show_module_compare'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_hover')[$store_id])) {
            $data['show_module_hover'] = (int) $this->config->get('module_ptcontrolpanel_module_hover')[$store_id];
        } else {
            $data['show_module_hover'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_quickview')[$store_id])) {
            $data['show_module_quickview'] = (int) $this->config->get('module_ptcontrolpanel_module_quickview')[$store_id];
        } else {
            $data['show_module_quickview'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_label')[$store_id])) {
            $data['show_module_label'] = (int) $this->config->get('module_ptcontrolpanel_module_label')[$store_id];
        } else {
            $data['show_module_label'] = 0;
        }

        /* Module Settings */
        $lang_code = $this->session->data['language'];
        
        if(isset($setting['module_title'])) {
            if(isset($setting['module_title'][$lang_code])) {
                $data['title'] = $setting['module_title'][$lang_code];
            } else {
                $data['title'] = false;
            }
        } else {
            $data['title'] = false;
        }

        if(isset($setting['module_type'])) {
            $data['module_type'] = $setting['module_type'];
        } else {
            $data['module_type'] = '';
        }

        if(isset($setting['show_module_description'])) {
            $show_module_des = (int) $setting['show_module_description'];
            if($show_module_des) {
                if(isset($setting['module_description'])) {
                    if(isset($setting['module_description'][$lang_code])) {
                        $data['module_des'] = html_entity_decode($setting['module_description'][$lang_code], ENT_QUOTES, 'UTF-8');
                    } else {
                        $data['module_des'] = false;
                    }
                } else {
                    $data['module_des'] = false;
                }
            } else {
                $data['module_des'] = false;
            }
        } else {
            $data['module_des'] = false;
        }

        // Layout Settings
        if(isset($setting['layout_type'])) {
            $data['layout_type'] = $setting['layout_type'];
        } else {
            $data['layout_type'] = '';
        }

        // Slider Settings
        $slider_width = $setting['slider_width'];

        $slider_height = $setting['slider_height'];

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['item'])) {
            $item = $setting['item'];
        } else {
            $item = array(
                'desktop' => 4,
                'laptop'  => 4,
                'tablet'  => 3,
                'mobile'  => 2
            );
        }

        if (isset($setting['row'])) {
            $row = (int) $setting['row'];
        } else {
            $row = 1;
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 500;
        }

        if (isset($setting['navigation']) && $setting['navigation']) {
            $navigation = true;
        } else {
            $navigation = false;
        }

        if (isset($setting['pagination']) && $setting['pagination']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        $data['slide'] = array(
            'width' => $slider_width,
            'height' => $slider_height,
            'auto'  => $auto,
            'desktop' => $item['desktop'],
            'laptop' => $item['laptop'],
            'tablet' => $item['tablet'],
            'mobile' => $item['mobile'],
            'speed' => $speed,
            'row'  => $row,
            'navigation' => $navigation,
            'pagination' => $pagination
        );

        // Products Collection
        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        $use_hover_image = $data['show_module_hover'];

        $new_results = $this->model_catalog_product->getLatestProducts(10);

        // Single Tab
        $data['single_products'] = array();
        $single_collection_type = $setting['single_product_collection'];

        if($single_collection_type == 'specified') {
            if(isset($setting['single_specified_products'])) {
                $single_specified_products = $setting['single_specified_products'];
            } else {
                $single_specified_products = false;
            }

            if($single_specified_products) {
                $products = array_slice($single_specified_products, 0, $limit);

                foreach ($products as $pid) {
                    $product = $this->getProductData($pid, $use_hover_image, $new_results, $slider_width, $slider_height);

                    if($product) {
                        $data['single_products'][] = $product;
                    }
                }
            }
        }

        if($single_collection_type == 'category') {
            $single_category_id = (int) $setting['single_category'];
            $single_category_product_collection = $setting['single_category_product_type'];

            if($single_category_product_collection == 'all') {
                $filter_data = array(
                    'filter_category_id' => $single_category_id,
                    'start' => 0,
                    'limit' => 10
                );

                $results = $this->model_catalog_product->getProducts($filter_data);
                if($results) {
                    foreach ($results as $result) {
                        $product = $this->getProductData($result['product_id'], $use_hover_image, $new_results, $slider_width, $slider_height);

                        if($product) {
                            $data['single_products'][] = $product;
                        }
                    }
                }
            }

            if($single_category_product_collection == 'specified') {
                if(isset($setting['single_category_products'])) {
                    $single_category_products = $setting['single_category_products'];
                } else {
                    $single_category_products = false;
                }

                if($single_category_products) {
                    $products = array_slice($single_category_products, 0, $limit);

                    foreach ($products as $pid) {
                        $product = $this->getProductData($pid, $use_hover_image, $new_results, $slider_width, $slider_height);

                        if($product) {
                            $data['single_products'][] = $product;
                        }
                    }
                }
            }

            if($single_category_product_collection == 'special') {
                if(isset($setting['single_category_product_special_type']) && $setting['single_category_product_special_type'] != '') {
                    $single_category_product_special_type = $setting['single_category_product_special_type'];
                } else {
                    $single_category_product_special_type = false;
                }

                if($single_category_product_special_type) {
                    $results = array();

                    if($single_category_product_special_type == 'mostviewed') {
                        $results = $this->model_plaza_catalog->getMostViewedByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'bestseller') {
                        $results = $this->model_plaza_catalog->getBestSellerProductsByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'special') {
                        $results = $this->model_plaza_catalog->getProductSpecialsByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'latest') {
                        $results = $this->model_plaza_catalog->getLatestProductsByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'random') {
                        $results = $this->model_plaza_catalog->getRandomByCategory($limit, $single_category_id);
                    }

                    if($results) {
                        foreach ($results as $result) {
                            $product = $this->getProductData($result['product_id'], $use_hover_image, $new_results, $slider_width, $slider_height);

                            if($product) {
                                $data['single_products'][] = $product;
                            }
                        }
                    }
                }
            }
        }

        if($single_collection_type == 'special') {
            if(isset($setting['single_product_special_type']) && $setting['single_product_special_type'] != '') {
                $single_product_special_type = $setting['single_product_special_type'];
            } else {
                $single_product_special_type = false;
            }

            if($single_product_special_type) {
                $results = array();

                if($single_product_special_type == 'mostviewed') {
                    $results = $this->model_plaza_catalog->getMostViewed($limit);
                }

                if($single_product_special_type == 'bestseller') {
                    $results = $this->model_catalog_product->getBestSellerProducts($limit);
                }

                if($single_product_special_type == 'special') {
                    $results = $this->model_catalog_product->getProductSpecials(array('start' => 0, 'limit' => $limit));
                }

                if($single_product_special_type == 'latest') {
                    $results = $this->model_catalog_product->getLatestProducts($limit);
                }

                if($single_product_special_type == 'random') {
                    $results = $this->model_plaza_catalog->getRandom($limit);
                }

                if($results) {
                    foreach ($results as $result) {
                        $product = $this->getProductData($result['product_id'], $use_hover_image, $new_results, $slider_width, $slider_height);

                        if($product) {
                            $data['single_products'][] = $product;
                        }
                    }
                }
            }
        }

        // Multi Tabs

        
        return $this->load->view('plaza/module/ptproducts', $data);
    }

    public function getProductData($product_id, $use_hover, $new_products, $width, $height) {
        $store_id = $this->config->get('config_store_id');
        
        $result = $this->model_plaza_catalog->getProduct($product_id);

        if($result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $width, $height);
            } else {
                $image = false;
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }

            if($result['description']) {
                $description = utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
            } else {
                $description = false;
            }

            if (isset($result['date_start']) && $result['date_start']) {
                $date_start = $result['date_start'];
            } else {
                $date_start = false;
            }

            if(isset($result['date_end']) &&  $result['date_end']) {
                $date_end = $result['date_end'];
            } else {
                $date_end = false;
            }

            $is_new = false;
            if ($new_products) {
                foreach($new_products as $new_r) {
                    if($result['product_id'] == $new_r['product_id']) {
                        $is_new = true;
                    }
                }
            }

            if($use_hover == 1) {
                $rotate_image = $this->model_plaza_rotateimage->getProductRotateImage($product_id);

                if($rotate_image) {
                    $rotate_image = $this->model_tool_image->resize($rotate_image, $width, $height);
                } else {
                    $rotate_image = false;
                }
            } else {
                $rotate_image = false;
            }

            /* Catalog Settings */
            if(isset($this->config->get('module_ptcontrolpanel_module_price')[$store_id])) {
                $show_module_price = (int) $this->config->get('module_ptcontrolpanel_module_price')[$store_id];
            } else {
                $show_module_price = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_module_cart')[$store_id])) {
                $show_module_cart = (int) $this->config->get('module_ptcontrolpanel_module_cart')[$store_id];
            } else {
                $show_module_cart = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_module_wishlist')[$store_id])) {
                $show_module_wishlist = (int) $this->config->get('module_ptcontrolpanel_module_wishlist')[$store_id];
            } else {
                $show_module_wishlist = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_module_compare')[$store_id])) {
                $show_module_compare = (int) $this->config->get('module_ptcontrolpanel_module_compare')[$store_id];
            } else {
                $show_module_compare = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_module_hover')[$store_id])) {
                $show_module_hover = (int) $this->config->get('module_ptcontrolpanel_module_hover')[$store_id];
            } else {
                $show_module_hover = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_module_quickview')[$store_id])) {
                $show_module_quickview = (int) $this->config->get('module_ptcontrolpanel_module_quickview')[$store_id];
            } else {
                $show_module_quickview = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_module_label')[$store_id])) {
                $show_module_label = (int) $this->config->get('module_ptcontrolpanel_module_label')[$store_id];
            } else {
                $show_module_label = 0;
            }

            $data = array(
                'product_id'    => $result['product_id'],
                'thumb'   	    => $image,
                'rotate_image'  => $rotate_image,
                'name'    	    => $result['name'],
                'price'   	    => $price,
                'special' 	    => $special,
                'is_new'        => $is_new,
                'date_start'  	=> $date_start,
                'date_end'    	=> $date_end,
                'rating'        => $rating,
                'description'   => $description,
                'show_module_price' => $show_module_price,
                'show_module_cart'  => $show_module_cart,
                'show_module_wishlist'  => $show_module_wishlist,
                'show_module_compare'  => $show_module_compare,
                'show_module_hover'  => $show_module_hover,
                'show_module_quickview'  => $show_module_quickview,
                'show_module_label'  => $show_module_label,
                'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
            );

            $html_content = $this->load->view('plaza/module/ptproducts/content', $data);

            $data['html'] = $html_content;



            return $data;
        } else {
            return false;
        }
    }
}