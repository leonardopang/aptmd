<?php
if ( ! defined( 'ABSPATH' ) ) {
	
}

class APTMD_User_Profile {

	public function __construct() {
		add_shortcode( 'user_profile', array( $this, 'aptmd_user_profile_shortcode' ) );
	}

	public function aptmd_get_field( $field_name ) {
		return get_user_meta( get_current_user_id(), $field_name, true );
	}

	public function aptmd_user_profile_shortcode() {
		if(!is_user_logged_in()){
            wp_redirect(home_url());
            
        }

		global $wpdb;
		ob_start();
		$user = get_userdata( get_current_user_id() );

		if ( isset( $_POST['submitProfile'] ) ) {
			$first_name_input  = $_POST['first_name'];
			$last_name_input   = $_POST['last_name'];
			$display_name_input = $_POST['display_name'];

			update_user_meta( get_current_user_id(), 'first_name', $first_name_input );
			update_user_meta( get_current_user_id(), 'last_name', $last_name_input );
			wp_update_user(
				array(
					'ID'           => get_current_user_id(),
					'display_name' => $display_name_input,
				)
			);

			if ( $_POST['password'] != '' && $_POST['password'] == $_POST['password_confirm'] && strlen( $_POST['password'] ) >= 8 ) {
				wp_set_password( $_POST['password'], get_current_user_id() );
			}else{
                echo "Senha ou confirmação inválida";
            }

			$user_email = $user->user_email;

		}
        $nickname = trim( $this->aptmd_get_field( 'nickname' ) );
        $first_name = trim( $this->aptmd_get_field( 'first_name' ) );
        $last_name = trim( $this->aptmd_get_field( 'last_name' ) );
        $display_name = $user->display_name;
        $first_last_name = $first_name . ' ' . $last_name;
        $last_first_name = $last_name . ' ' . $first_name;

        ?>
        <form action="<?php echo home_url( '/perfil' ) ?>" class="form_profile" method="post">
            <section class="name_section">
                <h1 class="name_header">Informações Pessoais</h1>
                <h1 class="profile_label" >Número de Sócio: <?php echo $this->aptmd_get_field('Socio');?></h1>
                <p style="font-size:14px; color: gray; margin: 0;">Se tu não consegues ver teu numero de sócio, retorne a sessão "Validar Quotas"</p>
                <?php
                $certs = $this->aptmd_get_field('certificados');
                if($certs):?>
                    <h1 class="profile_label" >Créditos para Certificados TMD: <?php echo $certs; ?></h1>
                <?php endif;
                $canal = $this->aptmd_get_field('canalizacao');
                if($canal):?>
                    <h1 class="profile_label" >Créditos para Certificados Canalização: <?php echo $canal; ?></h1>
                <?php endif;?>

                <label class="profile_label" for="first_name">Nome:</label><br>
                <input class="profile_input" type="text" name="first_name" value="<?php echo $this->aptmd_get_field( 'first_name' ) ?>"><br>
                <label class="profile_label" for="last_name">Sobrenome:</label><br>
                <input class="profile_input" type="text" name="last_name" value="<?php echo $last_name ?>"><br>
                <label class="profile_label" for="display_name">Nome preferido (Usado na emissão de certificados):</label><br>
                <input class="profile_input" type="text" name="display_name" value="<?php echo $display_name ?>"><br>                  
            </section>
            <section class="password_section">
                <h1 class="name_header">Alterar senha</h1>
                <label class="profile_label" for="password">Nova senha:</label><br>
                <input class="profile_input" type="password" name="password"><br>
                <label class="profile_label" for="password_confirm">Confirme a nova senha:</label><br>
                <input class="profile_input" type="password" name="password_confirm"><br>
            </section>
            <input class="profile_submit" type="submit" name="submitProfile" value="Atualizar Perfil">
            </form>
            <style>
                .form_profile {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                }

                .name_header, .contact_header {
                font-size: 1.5em;
                color: #4992ce;
                margin: 0;
                margin-bottom: 15px;
                margin-top: 25px;
                }

                .profile_label {
                font-size: 1.2em;
                color: #4992ce;
                margin-bottom: 15px;
                }

                .profile_input, .profile_select {
                font-size: 1.2em;
                padding-top: 5px;
                padding-bottom: 5px;
                padding-left: 10px;
                padding-right: 60px;
                border: 1px solid #4992ce !important;
                border-radius: 10px !important;
                margin-bottom: 15px;
                width: 100%;
                }

                .subheader {
                font-size: 0.8em;
                color: #777777;
                margin-bottom: 20px;
                }
                input[type="submit"] {
                font-size: 1.2em;
                color: white;
                background-color: #4992ce;
                border: none;
                border-radius: 5px;
                padding: 10px 20px;
                cursor: pointer;
                }

                input[type="submit"]:hover {
                background-color: #4992ce;
                }
            </style>
            <?php
            return ob_get_clean();
        }
}

new APTMD_User_Profile();

?>