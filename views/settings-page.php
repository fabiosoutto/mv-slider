<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <!-- inserindo abas no admin-->
    <?php
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'main_options';
    ?>
    <h2 class="nav-tab-wrapper">
        <a href="?page=mv_slider_admin&tab=main_options" class="nav-tab <?php echo $active_tab == 'main_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Main Options', 'mv-slider' ); ?></a>
        <a href="?page=mv_slider_admin&tab=additional_options" class="nav-tab <?php echo $active_tab == 'additional_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Additional Options', 'mv-slider' ); ?></a>
    </h2>
    <form action="options.php" method="post">
    <?php
        //condicional para exibir o conteúdo correto nas abas do admin
        if( $active_tab == 'main_options' ){

            //funções que gravam os dados no DB e exibem os dados no front
            settings_fields( 'mv_slider_group' );
            //mostrar as abas das páginas
            do_settings_sections( 'mv_slider_page1' );
        }else{
            settings_fields( 'mv_slider_group' );
            do_settings_sections( 'mv_slider_page2' );
        }
        //botão que envia e salva os dados na tabela
        submit_button( esc_html__( 'Save Settings', 'mv-slider' ) );
    ?>  
    </form>
</div>