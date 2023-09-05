<?php

if( !class_exists( 'MV_Slider_Post_Type' ) ){
    class MV_Slider_Post_Type{
        function __construct(){
            add_action( 'init', array( $this, 'create_post_type' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
            add_filter( 'manage_mv-slider_posts_columns', array( $this, 'mv_slider_cpt_columns' ) );
            add_action( 'manage_mv-slider_posts_custom_column', array( $this, 'mv_slider_custom_columns'), 10, 2 );
            add_filter( 'manage_edit-mv-slider_sortable_columns', array( $this, 'mv_slider_sortable_columns' ) );
        }

        public function create_post_type(){
            register_post_type(
                'mv-slider',
                array(
                    'label' => esc_html__( 'Slider', 'mv-slider' ),
                    'description' => esc_html__( 'Sliders', 'mv-slider' ),
                    'labels' => array(
                        'name' => esc_html__( 'Sliders', 'mv-slider' ),
                        'singular_name' => esc_html__( 'Slider', 'mv-slider' )
                    ),
                    'public' => true,
                    'supports' => array( 'title', 'editor', 'thumbnail', /*'page-attributes'*/ ),
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_in_menu' => false,
                    'menu_position' => 25,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export' => true,
                    'has_archive' => false,
                    'exclude_from_search' => false,
                    'publicly_queryable' => true,
                    'show_in_rest' => true,
                    'menu_icon' => 'dashicons-images-alt2',
                    //'register_meta_box_cb' => array( $this, 'add_meta_boxes' ),
                )
            );
        }

        //Método para ordenação das colunas
        public function mv_slider_sortable_columns( $columns ){
            $columns['mv_slider_link_text'] = 'mv_slider_link_text';
            return $columns;
        }

        //método que cria duas novas colunas no CPT no admin 
        public function mv_slider_cpt_columns( $columns ){
            $columns['mv_slider_link_text'] = esc_html__( 'Link Text', 'mv-slider' );
            $columns['mv_slider_link_url'] = esc_html__( 'Link URL', 'mv-slider' );
            return $columns;    
        } 

        //método que captura as infos para exibir nas novas colunas no CPT, no admin
        public function mv_slider_custom_columns( $column, $post_id ){
            switch( $column ){
                case 'mv_slider_link_text':
                    echo esc_html( get_post_meta( $post_id, 'mv_slider_link_text', true ) );
                break;
                case 'mv_slider_link_url':
                    echo esc_url( get_post_meta( $post_id, 'mv_slider_link_url', true ) );
                break;
            }
        }

        //método que cria uma metabox para o CPT
        public function add_meta_boxes(){
            add_meta_box(
                'mv_slider_meta_box',
                esc_html__( 'Link Options', 'mv-slider' ),
                array( $this, 'add_inner_meta_boxes' ),
                'mv-slider',
                'normal',  //'side' coloca na barra lateral
                'high'
            );
        }

        //este método define a var global criada e aponta o caminho para o cpt
        public function add_inner_meta_boxes( $post ){
            require_once( MV_SLIDER_PATH . 'views/mv-slider_metabox.php' );
        }

        //método que salva os dados enviados no form para o DB
        public function save_post( $post_id ){

            //verificando o token nonce no form
            if( isset( $_POST['mv_slider_nonce'] ) ){
                if( ! wp_verify_nonce( $_POST['mv_slider_nonce'], 'mv_slider_nonce' ) ){
                    return;
                }
            }

            //verifica e interrompe se o WP está fazendo auto-save
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }

            //verifica se estamos na página correta do CPT e se o user tem permissão de acesso(admin)
            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'mv-slider' ){
                if( ! current_user_can( 'edit_page', $post_id ) ){
                    return;
                }elseif( ! current_user_can( 'edit_post', $post_id ) ){
                    return;
                }
            }

            //verifica se os dados foram enviados pelo form...
            if( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ){

                //as vars que vão armazenar estes dados dos inputs
                $old_link_text = get_post_meta( $post_id, 'mv_slider_link_text', true );
                $new_link_text = sanitize_text_field( $_POST['mv_slider_link_text'] );
                $old_link_url = get_post_meta( $post_id, 'mv_slider_link_url', true );
                $new_link_url = esc_url_raw( $_POST['mv_slider_link_url'] );

                //enviando os dados e salvando no DB...
                //antes verificando se os campos estão vazios e resolvendo com valor default...
                if( empty( $new_link_text )){
                    update_post_meta( $post_id, 'mv_slider_link_text', esc_html__( 'Add some text', 'mv-slider' ) );
                } else {
                    update_post_meta( $post_id, 'mv_slider_link_text', $new_link_text, $old_link_text );
                }
                
                if( empty( $new_link_url )){
                    update_post_meta( $post_id, 'mv_slider_link_url', '#' );
                } else {
                    update_post_meta( $post_id, 'mv_slider_link_url', $new_link_url, $old_link_url );
                }
                
            }
        }
    }
}