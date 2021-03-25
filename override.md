## Minimum order per seller for free shipping module

###Setup:
  1. Add seller custom field named min_order_free_shipping with default value, the value can be customised per seller afterward
  2. Make sure flat shipping is enabled

### Code change:
1. Added `opencart_codebase/catalog/model/extension/catalog/override.php`

2. File: opencart_storage/modification/catalog/controller/checkout/cart.php
   Where: around line: 151 within `$data['products'][] = array(` block
   Added:
        `'seller_id'   => $product['seller_id'],`
   
3.  File: opencart_storage/modification/catalog/controller/checkout/cart.php 
    Where: around line: 165 after `foreach ($products as $product) {..}` block
    Added: 
    
   ```phpregexp
   
        $seller_ids = array_unique(array_values(array_column($products, 'seller_id')));
        $this->load->model('extension/catalog/override');
        $sellers_data = $this->model_extension_catalog_override->sellerMinOrderFreeShipping($seller_ids);
        $this->load->language('account/ptsregister');
        $data['text_seller_label'] = $this->language->get('text_seller_label');
    
        foreach ($data['products'] as $product) {
            $sellers_data[$product['seller_id']]['cart_price'] = $sellers_data[$product['seller_id']]['cart_price'] ?? 0;
            $sellers_data[$product['seller_id']]['cart_price'] += preg_replace('/[^0-9-.]+/', '',$product['total']);
        }
    
        $data['sellers_data'] = $sellers_data;
    ```

4.  File: opencart_storage/modification/system/library/cart/cart.php
    Where: around line: 76 after `foreach ($products as $product) {..}` block
    Replaced:    
        `$cart_query` with     
`        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart cart LEFT JOIN " . DB_PREFIX . "purpletree_vendor_products pvp ON cart.product_id = pvp.product_id WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY pvp.seller_id");
`
5. File: opencart_storage/modification/system/library/cart/cart.php
   Where: around line: 283 within `$product_data[] = array(` block
   Added:
   `'seller_id'       => $cart['seller_id'],,`
    
6. File: opencart_codebase/catalog/view/theme/default/template/checkout/cart.twig
    
   Where: around line: 50 
   Replaced:
   `{% for product in products %}`
    with
   
    ```phpt
   
       {% set current_seller_id = 0 %}
       {% for product in products %}
       {% if current_seller_id != product.seller_id %}
       {% set current_seller_id = product.seller_id %}
       <tr>
       <td colspan="6">
       {{ text_seller_label }}&nbsp; {{ sellers_data[product.seller_id]['store_name'] }}
       &nbsp;&nbsp;
       <small class="bg-{{ sellers_data[product.seller_id]['minimum_order_value'] <= sellers_data[product.seller_id]['cart_price'] ? 'success' : 'danger'  }}">Min. order {{ sellers_data[product.seller_id]['minimum_order_value'] }} for free shipping</small>
       </td>
       </tr>
       {% endif %}
   ```
     
7. File: opencart_storage/modification/catalog/controller/checkout/checkout.php
    
   Where: around line: 39 
   Added:

   ```phpregexp
   
       $seller_ids = array_unique(array_values(array_column($products, 'seller_id')));
       $this->load->model('extension/catalog/override');
       $sellers_data = $this->model_extension_catalog_override->sellerMinOrderFreeShipping($seller_ids);

        foreach ($products as $product) {
            $sellers_data[$product['seller_id']]['cart_price'] = $sellers_data[$product['seller_id']]['cart_price'] ?? 0;
            $sellers_data[$product['seller_id']]['cart_price'] += $product['total'];
        }
        $flat_shipping_multiplier = 0;
        foreach ($sellers_data as $seller_id => $seller_data) {
            if ($seller_data['minimum_order_value'] > $seller_data['cart_price']) {
                $flat_shipping_multiplier += 1;
            }
        }
        $this->session->data['flat_shipping_multiplier'] = $flat_shipping_multiplier;
   
   ```
     
8. File: opencart_codebase/catalog/model/extension/shipping/flat.php
    
   Where: around line: 20 
   Replaces: 
   ` $quote_data['flat'] = array(` block 
    with
   ```phpregexp
   
       $flat_shipping_multiplier = $this->session->data['flat_shipping_multiplier'] ?? 1;
        $cost = $this->config->get('shipping_flat_cost') * $flat_shipping_multiplier;
        $quote_data['flat'] = array(
            'code'         => 'flat.flat',
            'title'        => $this->language->get('text_description'),
            'cost'         => $cost,
            'tax_class_id' => $this->config->get('shipping_flat_tax_class_id'),
            'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_flat_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
        );
   
   ```
       