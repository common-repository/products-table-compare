<?php


include_once('ProductsTableCompare_ShortCodeLoader.php');

class ProductsTableCompare_draw_table extends ProductsTableCompare_ShortCodeLoader
{
    /**
     * @param  $atts shortcode inputs
     * @return string shortcode content
     */
    public function handleShortcode($atts)
    {

        $attr_list = [];
        if (isset($atts['attrs'])) {
            $attr_list = explode(",", $atts['attrs']);
        }

        $info_list =  [];
        if (isset($atts['info'])) {
            $info_list = explode(",", $atts['info']);
        }

        $acf_list = [];
        if (isset($atts['acf'])) {
            $acf_list = explode(",", $atts['acf']);
        }

        if (isset($atts['swatches'])) {
            $swatches = $atts['swatches'];
        } else{
            $swatches = 0;
        }


        if (isset($atts['cat']) && !empty($atts['cat'])) {
            $products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => array(array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'     =>  array_map('intval', explode(',', $atts['cat'])),
                    'operator'  => 'IN'
                ))
            ));
        } else {
            $products = get_posts(array('post_type' => 'product',  'posts_per_page' => -1));
        }


        $products_index = 0;
        $product_object = array();
        foreach ($products as $product) {
            $new_prod_object = array();
            
        
            $new_prod_object['name'] = $product->post_name;
            $new_prod_object['ID'] = $product->ID;
            $new_prod_object['image'] = get_the_post_thumbnail_url($product->ID);
            $new_prod_object['index'] = $products_index;
            if(!empty($info_list)){
                $info =  array();
                $prod = wc_get_product($product->ID);
                foreach ($info_list as $info_attr) {
                    if($info_attr == 'weight'){
                       $info['weight'] = $prod->get_weight(); 
                    }
                    if($info_attr == 'price'){
                       $info['price'] = $prod->get_price(); 
                    }
                    if($info_attr == 'length'){
                       $info['length'] = $prod->get_length(); 
                    }
                    if($info_attr == 'width'){
                       $info['length'] = $prod->get_width(); 
                    }
                    if($info_attr == 'height'){
                       $info['length'] = $prod->get_height(); 
                    }
                }
                $new_prod_object['info'] = $info;
            }
            
            
            if (!empty($acf_list)) {
                $acfs = array();
                foreach ($acf_list as $acf_field) {
                    $acfs[$acf_field] = get_field( $acf_field, $product->ID);
                }
                $new_prod_object['acfs'] = $acfs;
            }
            
            if (!empty($attr_list)) {
                $cells = array();
                foreach ($attr_list as $att) {
                    $table_cells = array();
                    $table_cells['cell_name'] = $att;
                    $table_cells['cell_data'] = wc_get_product_terms($product->ID, 'pa_' . $att . '');
                    if($swatches && $att == 'color'){
                        foreach($table_cells['cell_data'] as $color){
                            $terms = get_term_meta($color->term_id, false);
                            $color->hex = $terms['product_attribute_color'];
                        }
                    }
                    $cells[] = $table_cells;
                }
                $new_prod_object['atributes'] = $cells;
            }
            $product_object[] = $new_prod_object;
            $products_index++;
        }
            wp_reset_postdata();
        
        
            $table_result = json_encode(array('all_products' => $product_object));




?> <div>
            <script>
                const table = new PTC_CompareTable()
                window.addEventListener('DOMContentLoaded', (event) => {

                    table.getProducts('<?php echo $table_result ?>')
                })
            </script>
            <div id="ptc-actions">
                <button onclick="table.filterTable()">Filter</button>
                <button onclick="table.resetTable()">Reset</button>
            </div>
            <div id="ptc-content">
                <table>
                    <thead id="ptc-thead"></thead>
                    <tbody id="ptc-tbody"> </tbody>
                </table>
            </div>
        </div>
<?php
        return;
    }
}
