<?php

if( ! class_exists( 'MV_Slider_Settings' ) ){
    class MV_Slider_Settings{
        //método estático para poder-mos usar fora desta classe...
        public static $options;

        //criando o contrutor da classe...
        public function __construct(){
            self::$options = get_option( 'mv_slider_options' );

            //gancho que dispara a função para poder-mos criar/usar o form
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }

        //método do hook admin_init...
        public function admin_init(){

            //chamando a função de agrupamento e chave de registro dos dados no DB
            register_setting( 'mv_slider_group', 'mv_slider_options', array( $this, 'mv_slider_validate' ) );

            //criando a 1a seção...
            add_settings_section(
                'mv_slider_main_section',
                esc_html__( 'How does it work?', 'mv-slider' ),
                null,
                'mv_slider_page1'
            );

            //criando a 2a seção...
            add_settings_section(
                'mv_slider_second_section',
                esc_html__( 'Other Plugin Options', 'mv-slider' ),
                null,
                'mv_slider_page2'
            );

            //criando um campo dentro da seção 1...
            add_settings_field(
                'mv_slider_shortcode',
                esc_html__( 'Shortcode', 'mv-slider' ),
                array( $this, 'mv_slider_shortcode_callback' ),
                'mv_slider_page1',
                'mv_slider_main_section'
            );

            //criando um campo texto dentro da seção 2...
            add_settings_field(
                'mv_slider_title',
                esc_html__( 'Slider Title', 'mv-slider' ),
                array( $this, 'mv_slider_title_callback' ),
                'mv_slider_page2',
                'mv_slider_second_section',
                array(
                    'label_for' => 'mv_slider_title'
                )
            );

            //criando um campo checkbox dentro da seção 2...
            add_settings_field(
                'mv_slider_bullets',
                esc_html__( 'Display Bullets', 'mv-slider' ),
                array( $this, 'mv_slider_bullets_callback' ),
                'mv_slider_page2',
                'mv_slider_second_section',
                array(
                    'label_for' => 'mv_slider_bullets'
                )
            );

            //criando um campo select dentro da seção 2...
            add_settings_field(
                'mv_slider_style',
                esc_html__( 'Slider Style', 'mv-slider' ),
                array( $this, 'mv_slider_style_callback' ),
                'mv_slider_page2',
                'mv_slider_second_section',
                array(
                    'items' => array(
                        'style-1',
                        'style-2'  
                    ),
                    'label_for' => 'mv_slider_style'
                )
            );
        }


        //criando a função CB que cria o 1o campo (texto explicativo) dentro da seção 1...
        public function mv_slider_shortcode_callback(){
            ?>
            <span><?php esc_html_e( 'Use the shortcode [mv_slider] to display the slider in any page/post/widget', 'mv-slider' ); ?></span>
            <?php
        }

        //criando a função CB que cria o 1o campo (text) dentro da seção 2...
        public function mv_slider_title_callback( $args ){
            ?>
                <input type="text" 
                    name="mv_slider_options[mv_slider_title]" 
                    id="mv_slider_title" 
                    value="<?php echo isset( self::$options['mv_slider_title'] ) ? esc_attr( self::$options['mv_slider_title'] ) : ''; ?>"
                >
            <?php
        }

        //criando a função CB que cria o 2o campo (checkbox) dentro da seção 2...
        public function mv_slider_bullets_callback( $args ){
            ?>
                <input type="checkbox" 
                    name="mv_slider_options[mv_slider_bullets]" 
                    id="mv_slider_bullets" 
                    value="1"
                    <?php
                        if( isset( self::$options['mv_slider_bullets'] ) ){
                            checked( "1", self::$options['mv_slider_bullets'], true );
                        } 
                    ?>
                />
                <label for="mv_slider_bullets"><?php esc_html_e( 'Whether to display bullets or not', 'mv-slider' ); ?></label>
            <?php
        }

        //criando a função CB que cria o 3o campo (select) dentro da seção 2...
        public function mv_slider_style_callback( $args ){
            ?>
            <select 
                id="mv_slider_style" 
                name="mv_slider_options[mv_slider_style]">
                <?php
                    foreach( $args['items'] as $item ): 
                ?>
                    <option value="<?php echo esc_attr( $item ); ?>" 
                        <?php isset( self::$options['mv_slider_style'] ) ? selected( $item, self::$options['mv_slider_style'], true ) : ''; ?>
                    >
                    <?php echo esc_html( ucfirst( $item ) ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }

        //função CB de validação dos dados...
        public function mv_slider_validate( $input ){
            $new_input = array();
            foreach( $input as $key => $value ){
                switch ($key){
                    case 'mv_slider_title':
                        if( empty( $value )){
                            //msg de alerta pela falta de texto no titulo
                            add_settings_error( 'mv_slider_options', 'mv_slider_message', esc_html__( 'The title canot be left empty!', 'mv-slider' ), 'warning' );
                            $value = esc_html__( 'Please, type some text', 'mv-slider');
                        }
                        $new_input[$key] = sanitize_text_field( $value );
                    break;
                    default:
                        $new_input[$key] = sanitize_text_field( $value );
                    break;
                }
            }
            return $new_input;
        }
    }
}