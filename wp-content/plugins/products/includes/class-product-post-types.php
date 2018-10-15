<?php

/**
 * Definition of custom post types for products.
 *
 * @link       saniin.com
 * @since      1.0.0
 *
 * @package    Products
 * @subpackage Products/includes
 */

/**
 * Definition of custom post types for products.
 *
 * This class all the different post types required for the products module
 *
 * @since      1.0.0
 * @package    Products
 * @subpackage Products/includes
 * @author     Saniin <willmer.hg@gmail.com>
 */
class ProductPostTypes {

    /**
     * Define the main product post type.
     *
     * This method defines the main product post type
     *
     * @since    1.0.0
     */
    public static function products_post_type() {
        register_post_type('products', array(
            'labels' => array(
                'name' => 'Servicios',
                'singular_name' => 'servicio',
                'add_new' => 'Agregar',
                'add_new_item' => 'Agregar nuevo servicio',
                'edit_item' => 'Editar servicio',
                'new_item' => 'Nuevo servicio',
                'view_item' => 'Ver servicio',
                'view_items' => 'Ver servicios',
                'search_items' => 'Buscar servicios',
                'not_found' => 'Servicio no encontrado',
                'not_found_in_trash' => 'Servicio no encontrado en la papelera',
                'parent_item_colon' => 'Servicio padre',
                'all_items' => 'Todos los servicios',
                'archives' => 'Archivo de servicios',
                'attributes' => 'Atributos',
                'insert_into_item' => 'Insertar en servicio',
                'uploaded_to_this_item' => 'Subido para servicio',
                'featured_image' => 'Imagen de servicio',
                'set_featured_image' => 'Definir imagen de servicio',
                'remove_featured_image' => 'Remover imagen de servicio',
                'use_featured_image' => 'Usar como imagen de servicio'
            ),
            'public' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-hammer',
            'supports' => array(
                'title', 'editor', 'thumbnail', 'excerpt', 'comments'
            ),
            'has_archive' => true,
            'show_in_rest' => true,
        ));

        register_post_type('faqs', array(
            'labels' => array(
                'name' => 'FAQs',
                'singular_name' => 'FAQ',
                'add_new' => 'Agregar',
                'add_new_item' => 'Agregar nuevo FAQ',
                'edit_item' => 'Editar FAQ',
                'new_item' => 'Nuevo FAQ',
                'view_item' => 'Ver FAQ',
                'view_items' => 'Ver FAQs',
                'search_items' => 'Buscar FAQs',
                'not_found' => 'FAQ no encontrado',
                'not_found_in_trash' => 'FAQ no encontrado en la papelera',
                'parent_item_colon' => 'FAQ padre',
                'all_items' => 'Todos los FAQs',
                'archives' => 'Archivo de FAQs',
                'attributes' => 'Atributos',
                'insert_into_item' => 'Insertar en FAQ',
                'uploaded_to_this_item' => 'Subido para FAQ',
                'featured_image' => 'Imagen de FAQ',
                'set_featured_image' => 'Definir imagen de FAQ',
                'remove_featured_image' => 'Remover imagen de FAQ',
                'use_featured_image' => 'Usar como imagen de FAQ'
            ),
            'public' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-testimonial',
            'supports' => array(
                'title', 'editor'
            ),
            'has_archive' => true,
            'show_in_rest' => true,
        ));

        self::products_taxonomies();
    }

    /**
     * Define the main product post type's taxonomies.
     *
     * This method defines the main product post type
     *
     * @since    1.0.0
     */
    public static function products_taxonomies() {
        // Categories
        // Specifications
        register_taxonomy( 'sc_products_categories', 'products', array(
            'show_ui' => true,
            'show_in_rest' => true,
            'labels' => array(
                'name' => 'Categorias',
                'singular_name' => 'Categoria',
                'all_items' => 'Todas las categorias',
                'edit_item' => 'Editar categoria',
                'view_item' => 'Ver categoria',
                'update_item' => 'Actualizar categoria',
                'add_new_item' => 'Agregar nueva categoria',
                'new_item_name' => 'Nuevo nombre de categoria',
                'parent_item' => 'Categoria padre',
                'search_items' => 'Buscar categorias',
                'popular_items' => 'Categorias populares',
                'separate_items_with_commas' => 'Separar las categorias con comas (,)',
                'add_or_remove_items' => 'Agregar o eliminar categorias',
                'choose_from_most_used' => 'Elegir de las categorias mas usadas',
                'back_to_items' => 'Regresar a categorias',
            ),
            'hierarchical' => true
        ));
        register_taxonomy_for_object_type('sc_products_categories', 'products');

        register_taxonomy( 'sc_products_spec', 'products', array(
            'show_ui' => true,
            'show_in_rest' => true,
            'labels' => array(
                'name' => 'Especificaciones',
                'singular_name' => 'Especificacion',
                'all_items' => 'Todas las especificaciones',
                'edit_item' => 'Editar especificacion',
                'view_item' => 'Ver especificacion',
                'update_item' => 'Actualizar especificacion',
                'add_new_item' => 'Agregar nueva especificacion',
                'new_item_name' => 'Nuevo nombre de especificacion',
                'parent_item' => 'Especificacion padre',
                'search_items' => 'Buscar especificaciones',
                'popular_items' => 'Especificaciones populares',
                'separate_items_with_commas' => 'Separar las especificaciones con comas (,)',
                'add_or_remove_items' => 'Agregar o eliminar especificaciones',
                'choose_from_most_used' => 'Elegir de las especificaciones mas usadas',
                'back_to_items' => 'Regresar a especificaciones',
            ),
        ));
        register_taxonomy_for_object_type('sc_products_spec', 'products');
    }
    public static function products_meta_boxes() {
        $meta_boxes[] = array(
            'id' => 'sc-price-metabox',
            'title' => esc_html__( 'Características del servicio', 'sinchapus' ),
            'post_types' => array( 'products' ),
            'context' => 'after_title',
            'priority' => 'default',
            'autosave' => 'true',
            'fields' => array(
                array(
                    'id' => 'sc-product-price',
                    'type' => 'number',
                    'name' => esc_html__( 'Precio', 'sinchapus' ),
                    'desc' => esc_html__( 'Precio del servicio', 'sinchapus' ),
                    'placeholder' => esc_html__( '0.00', 'sinchapus' ),
                ),
                array(
                    'id' => 'sc-excecution-time',
                    'type' => 'number',
                    'name' => esc_html__( 'Tiempo de ejecución', 'sinchapus' ),
                    'desc' => esc_html__( 'Tiempo en minutos (m) que toma aproximadamente', 'sinchapus' ),
                    'placeholder' => esc_html__( '15 min', 'sinchapus' ),
                    'step' => 'any',
                ),
                array(
                    'id' => 'sc-warranty',
                    'type' => 'number',
                    'name' => esc_html__( 'Garantía', 'metabox-online-generator' ),
                    'desc' => esc_html__( 'Tiempo de garantía para el servicio en meses (m).', 'metabox-online-generator' ),
                    'placeholder' => esc_html__( '3 meses', 'metabox-online-generator' ),
                ),
            ),
        );

        $meta_boxes[] = array(
            'id' => 'sc-not-included-metabox',
            'title' => esc_html__( 'No incluye', 'sinchapus' ),
            'post_types' => array('products' ),
            'context' => 'after_editor',
            'priority' => 'default',
            'autosave' => 'false',
            'fields' => array(
                array(
                    'id' => 'sc-not-included',
                    'type' => 'wysiwyg',
                    'desc' => esc_html__( 'Texto que describe las exclusiones del servicio', 'sinchapus' ),
                ),
            ),
        );

        $meta_boxes[] = array(
            'id' => 'sc-request-details-metabox',
            'title' => esc_html__( 'Detalles de la solicitud', 'sinchapus' ),
            'post_types' => array( 'requests' ),
            'context' => 'after_title',
            'priority' => 'default',
            'autosave' => 'true',
            'fields' => array(
                array(
                    'id' => 'sc-request-date',
                    'type' => 'date',
                    'name' => esc_html__( 'Fecha a realizarse', 'sinchapus' ),
                    'desc' => esc_html__( 'La fecha en la cual se realizará el servicio', 'sinchapus' ),
                ),
                array(
                    'id' => 'sc-request-time',
                    'name' => esc_html__( 'Hora a realizarse', 'sinchapus' ),
                    'type' => 'time',
                    'desc' => esc_html__( 'La hora aproximada en la cual se podrá empezar el servicio.', 'sinchapus' ),
                ),
                array(
                    'id' => 'sc-request-address',
                    'type' => 'textarea',
                    'name' => esc_html__( 'Dirección', 'sinchapus' ),
                    'desc' => esc_html__( 'La dirección en donde se realizará el servicio.', 'sinchapus' ),
                    'placeholder' => esc_html__( '10 calle 10-00 zona 19, Guatemala', 'sinchapus' ),
                    'rows' => 3,
                ),
                array(
                    'id' => 'map_4',
                    'type' => 'map',
                    'name' => esc_html__( 'Map', 'sinchapus' ),
                    'desc' => esc_html__( 'La ubicación en donde se realizará el serivicio.', 'sinchapus' ),
                    'address_field' => 'sc-request-address',
                    'api_key' => 'AIzaSyDOrhvPcqIufDUIbJQro9X1mr59Lm5OxZ8',
                ),
            ),
        );

        return $meta_boxes;
    }

}
