=== Products Table Compare ===
Contributors: hippocampustech
Donate link: https://www.hippocampus.me/
Tags: products,table,compare,filter,product-info,filter-products
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.4
Requires PHP: 5.4
Tested up to: 5.6
Stable tag: 0.5

compare products with table, extention for woocommerce. add rows of product attributes, products shipping info and acf's

== Description ==

Products Table Compare is a plugin for websites and eshops that are using the "woocommerce" plugin.
By using the Products Table Compare shortcode you can generate a dinamic table of your products and populate it with
product attributs, product shipping mesertments and custom fields.
If your website or eShop is using the Variation Swatches for WooCommerce plugin, Products Table Compare allows you to switch
between displaying your product colors as text or as visual color swatches.






== Frequently Asked Questions ==

= How to incorporate Products Table Compare on youe site or eshop =

Place `<?php do_shortcode('products_compare'); ?>` or [products_compare] in your templates or files


= How do I filter the products table by product categories =

The default behavior of the Products Table Compare plugin is to show all products, in order to filter the products by product category
 you have to use the "cat" variable with your selected product category ID inside the short code, for exemple:

 `<?php do_shortcode('products_compare cat=1'); ?>` or [products_compare cat=1] - will only populate the table with products from the product ctegory with the ID "1".

In case you want to populate the tabke with more than onre product category, you should seperate the categories with a comma, for exemple:

`<?php do_shortcode('products_compare cat=1,2'); ?>` or [products_compare cat=1,2] - will only populate the table with products from the product categories of ID's "1" and "2".


= My table has only the head row but no information =

Thats becuase you didn't choose the product information for your table.

Basicly you have 3 types of product information: products shipping measerment and data (weight, height length and price)
woocommerce products attributes and advenced custom fields (acf plugin).


In order to populate the table with product data and measerment you need to use the data name in the info variable in the shortcode, for exemple:

`<?php do_shortcode('products_compare info=price'); ?>` or [products_compare info=price] - will populate the tabele with the price row.

In order to populate the table with more than one product data or measerments rows, you should seperate the info variable data with a comma, for exemple: 

`<?php do_shortcode('products_compare info=price,length'); ?>` or [products_compare info=price,length] - will populate the tabele with the price and length rows.


In order to populate the table with product attributes you need to use the attribule name in the attrs variable in the shortcode, for exemple:

`<?php do_shortcode('products_compare attrs=color'); ?>` or [products_compare attrs=color] - will populate the tabele with the color row.

In order to populate the table with more then one product attribues rows, you should seperate the attribues names with a comma in the attrs variable, for exemple: 

`<?php do_shortcode('products_compare attrs=color,size'); ?>` or [products_compare attrs=color,size] - will populate the tabele with the color and size rows.


In order to populate the table with advenced custom fields you first need to set up the "Advenced Custom Fields" plugin.
Then you need to use the acf slug that you set up in the acf variable in the shortcode, for exemple:

`<?php do_shortcode('products_compare acf=moter'); ?>` or [products_compare acf=motor] - will populate the tabele with the motor row.
be advised that the row name that will be shown in the left most cell in the row is the field slug that you set up

In order to populate the table with more then one product attribues rows, you should seperate the attribues names with a comma in the attrs variable, for exemple: 

`<?php do_shortcode('products_compare acf=motor,electric-system'); ?>` or [products_compare acf=motor,electric-system'] - will populate the tabele with the motor and electric-system rows.


= How do I set up the swatches option =

First, you will need to set up the "Variation Swatches for WooCommerce" plugin. 
Then you need to follow the variation swatches toturial and set up color hexes to your color product attributes **make sure you name the attribue "color" and not "colors"**. 
After you finished these steps you should incorporate the swatches option with the "swatches" variable and the property 1 in the shortcode, for exemple:

`<?php do_shortcode('products_compare attrs=color swatches=1'); ?>` or [products_compare attrs=color swatches=1] 

== Screenshots ==
1.  [products_compare cat=27 attrs=color info=weight,length acf=standard,range swatches=1]
2.  [products_compare cat=24 attrs=color info=weight,length,price swatches=1]
3.  [products_compare cat=36,35 attrs=color,size info=price  swatches=1]

== Changelog ==

= 0.5 =
- Initial Revision
- Public Beta
