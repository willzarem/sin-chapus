<?php

/**
 * Definition of custom post types for requests.
 *
 * @link       saniin.com
 * @since      1.0.0
 *
 * @package    Request
 * @subpackage Request/includes
 */

/**
 * Definition of custom post types for requests.
 *
 * This class all the different post types required for the requests module
 *
 * @since      1.0.0
 * @package    Request
 * @subpackage Request/includes
 * @author     Saniin <willmer.hg@gmail.com>
 */
class RequestPostTypes {

    /**
     * Define the product request post type.
     *
     * This method defines the product request post type
     *
     * @since    1.0.0
     */
    public static function requests_post_type() {
        register_post_type('requests', array(
            'labels' => array(
                'name' => 'Solicitudes de servicio',
                'singular_name' => 'solicitudes de servicio',
                'add_new' => 'Agregar',
                'add_new_item' => 'Agregar nuevo solicitudes de servicio',
                'edit_item' => 'Editar solicitudes de servicio',
                'new_item' => 'Nuevo solicitudes de servicio',
                'view_item' => 'Ver solicitudes de servicio',
                'view_items' => 'Ver solicitudes de servicio',
                'search_items' => 'Buscar solicitudes de servicio',
                'not_found' => 'Solicitudes de solicitudes de servicio no encontrado',
                'not_found_in_trash' => 'Solicitudes de solicitudes de servicio no encontrado en la papelera',
                'parent_item_colon' => 'Solicitudes de solicitudes de servicio padre',
                'all_items' => 'Todos los solicitudes de servicio',
                'archives' => 'Archivo de solicitudes de servicio',
                'attributes' => 'Atributos',
                'insert_into_item' => 'Insertar en solicitudes de servicio',
                'uploaded_to_this_item' => 'Subido para solicitudes de servicio',
                'featured_image' => 'Imagen de solicitudes de servicio',
                'set_featured_image' => 'Definir imagen de solicitudes de servicio',
                'remove_featured_image' => 'Remover imagen de solicitudes de servicio',
                'use_featured_image' => 'Usar como imagen de solicitudes de servicio'
            ),
            'public' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-clipboard',
            'supports' => array(
                'title', 'editor', 'thumbnail', 'excerpt', 'comments'
            ),
            'has_archive' => true,
            'show_in_rest' => true,
        ));
    }

    public static function requests_meta_boxes() {
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
