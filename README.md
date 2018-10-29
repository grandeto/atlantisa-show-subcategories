# Atlantisa Show Subcategories WordPress plugin

## Description

This WordPress plugin shows all subcategories (child categories) of a given post category via shortcode. Go to Settings -> AtlantisA Show Subcategories, setup the plugin according to your requirements and then use shortcode [atlantisa_showsubcat] in Posts -> Categories -> Category -> Description or implement it via: echo do_shortcode('[atlantisa_showsubcat]'); in your page template. It will not show anything if the visitor is not on Category page which has subcategories.

## Instalation

- Via WordPress dashboard: Create atlantisa-show-subcategories.zip from the .php files in the repo. Go to Plugins -> Add New -> Upload Plugin
- Via FTP: Go to /public_html/wp-content/plugins. Create "atlantisa-show-subcategories" folder. Upload in the created folder all .php files from the repo


## How to use

<ol>
    <li>Go to Posts -> Categories</li>
    <li>Create or Select desired Category and open to edit it</li>
    <li>In Edit Category -> Description add: [atlantisa_showsubcat]</li>
    <li>Save the changes</li>
    <li>Create subcategories (child categories) and asign them to this category</li>
    <li>Add this category to a menu or in post/page and open it from the front-end of your website. You should see its subcategories.</li>
    <li>If you don't see its subcategories maybe this is a theme issue and you need to implement the shordcode directly in your thame page template by adding: &lt?php echo do_shortcode( '[atlantisa_showsubcat]' ); ?&gt</li>
</ol>
