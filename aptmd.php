<?php

/**
 * Plugin Name: Aptmd Plugin
 * 
 */
include('KEYS.php');
function remove_admin_bar()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');


function abd_redirect()
{
    ob_start();

    global $wpdb;
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $prefix = $wpdb->prefix;


    $results = $wpdb->get_results("SELECT * FROM `wp_posts` where post_type like '%at_biz_dir%' and post_author = {$user_id}")[0];

    if ($results) {
        $url = "https://aptmd.org/enviar-listagem/edit/" . $results->ID;
    } else {
        $url = "https://aptmd.org/enviar-listagem/";
    }
    wp_redirect($url);

    return ob_get_clean();
}
add_shortcode('abd_redirect', 'abd_redirect');

include('load_csv.php');
include('add_socio_touser.php');
include('socio_card.php');
include('confirmar_socio.php');
include('validar_socio.php');
include('certificados.php');
include('validar_cert.php');
include('canalizacao.php');
include('listar_meus_certificados.php');
include('meus_canalizacao.php');
include('corrigir_datas.php');
include('resetpass.php');
include('emitir_relatorio.php');
include('listar_certificados_adm.php');
include('add_certs.php');
include('listar_canalizacao_adm.php');
include('corrigir_novo.php');
include('perfil.php');
include('vp_ticket.php');


if(!function_exists('display_php_error_for_admin')) {
    function display_php_error_for_admin()
    {
        $user_id = get_current_user_id();
        $user_meta = get_userdata($user_id);
        $roles = $user_meta->roles;
        if(is_array($roles)){
            if (in_array("administrator", $roles)) {
                error_reporting(0);
                @ini_set('display_errors', 0);
            } 
        }elseif ($roles == "administrator"){
            error_reporting(0);
            @ini_set('display_errors', 0);
        }

    }
}
add_action('init','display_php_error_for_admin');

function create_event()
{
    ob_start();

    $current_user = wp_get_current_user();
    global $wpdb;

    if (isset($_POST['event_creator']) && isset($_POST['event_creator_email']) && isset($_POST['event_title']) && isset($_POST['event_description']) && isset($_POST['event_start_date']) && isset($_POST['event_end_date']) && isset($_POST['event_start_hour']) && isset($_POST['event_end_hour']) && isset($_POST['categories']) && isset($_POST['zoom_required']) && isset($_POST['url'])) {
        $event_creator = $_POST['event_creator'];
        $event_creator_email = $_POST['event_creator_email'];
        $event_title = $_POST['event_title'];
        $event_description = $_POST['event_description'];
        $event_start_date = $_POST['event_start_date'];
        $event_end_date = $_POST['event_end_date'];
        $event_start_hour = $_POST['event_start_hour'];
        $event_end_hour = $_POST['event_end_hour'];
        $categories = intval( $_POST['categories']);
        $zoom_required = $_POST['zoom_required'];
        $url = $_POST['url'];
        $zoom = $_POST['zoom_required'];



        // echo $event_creator . '<br>'. $event_creator_email . '<br>'. $event_title . '<br>'. $event_description . '<br>'. $event_start_date . '<br>'. $event_end_date . '<br>'. $event_start_hour . '<br>'. $event_end_hour . '<br>'. $categories . '<br>'. $zoom_required . '<br>'. $url . '<br>';

        $start_hour = explode(':', $event_start_hour)[0];
        $end_hour = explode(':', $event_end_hour)[0];
        $start_minutes = explode(':', $event_start_hour)[1];
        $end_minutes = explode(':', $event_end_hour)[1];

        // $query = "SELECT * FROM `wp_posts` where post_type like '%organizer%' and post_title like '%".$event_creator."%'";
        // $organizer_found = $wpdb->get_results($query);
        // if($organizer_found) {
        //     $organizer_id = $organizer_found[0]->ID;
        // } else {
        //     $organizer_id = tribe_create_organizer(array(
        //         'Organizer' => $event_creator,
        //         'Email' => $event_creator_email,
        //     ));
        // }
        
        if($zoom == 'Sim'){
            wp_mail("novas@aptmd.org", 'Zoom', 'O evento '.$event_title.' ser realizado no zoom. <br> Email do Socio: '.$event_creator_email."");
        }

    
        $args = array(
            'post_title' => $event_title . " | " . $event_creator,
            'post_content' => $event_description." <br> <br><br> <a href='".$url."'>Link para o evento</a>",
            'post_status' => 'pending',
            'tax_input' => array(
                Tribe__Events__Main::TAXONOMY => array( 769, $categories ),
                ),
            'EventStartDate' => $event_start_date,
            'EventEndDate' => $event_end_date,
            'EventStartHour' => $start_hour,
            'EventStartMinute' => $start_minutes,
            'EventEndHour' => $end_hour,
            'EventEndMinute' => $end_minutes,
            'EventHideFromUpcoming' => false,
            'EventShowMapLink' => true,
            'EventShowMap' => true,
            'Organizer' => array(
                'Organizer' => $event_creator,
                'Email' => $event_creator_email,
                'post_status' => 'publish',
            ),
            'EventURL' => $url,
        );

        $result = tribe_create_event($args);
        update_post_meta($result, '_Event_Zoom', $zoom);

        // if (is_wp_error($result)) {
        //     // Event creation failed, display the error message
        //     echo 'Falha ao criar evento: ' . $result->get_error_message();
        // } else {
        //     // Event creation successful, display the new event ID
        //     echo 'Novo evento enviado com ID: ' . $result;
        // }
    }
?>

    <form class="add_event_form" action="" method="post">
        <label for="event_creator">Nome do Facilitador/Formador: <span class="requiredfield"> * </span></label>
        <input type="text" name="event_creator" id="event_creator" value="<?php echo $current_user->display_name; ?>" required><br>
        <label for="event_creator_email">Email do Facilitador/Formador: <span class="requiredfield"> * </span></label>
        <input type="email" name="event_creator_email" id="event_creator_email" value="<?php echo $current_user->user_email; ?>" required><br>

        <label for="event_title">Ttulo da Atividade: <span class="requiredfield"> * </span></label>
        <p class="sub_event_title">SOMENTE O TITULO DA ATIVIDADE</p>
        <input type="text" name="event_title" id="event_title" required placeholder="Exemplo: Workshop de Terapia Multidimensional"><br>
        <label for="event_description">Descrio da Atividade: <span class="requiredfield"> * </span></label>
        <textarea name="event_description" id="event_description" cols="30" rows="10" required></textarea><br>
        <div class="dates">
            <label for="event_start_date">Data de Início: <span class="requiredfield"> * </span></label>
            <input type="date" name="event_start_date" id="event_start_date" required><br>
            <label for="event_end_date">Data de Fim: <span class="requiredfield"> * </span></label>
            <input type="date" name="event_end_date" id="event_end_date" required><br>
        </div>
        <div class="hours">
            <label for="event_start_hour">Hora de Início: <span class="requiredfield"> * </span></label>
            <input type="time" name="event_start_hour" id="event_start_hour" required><br>
            <label for="event_end_hour">Hora de Encerramento: <span class="requiredfield"> * </span></label>
            <input type="time" name="event_end_hour" id="event_end_hour" required><br>
        </div>
        <label for="categories">Categorias:<span class="requiredfield"> * </span></label>
        <select name="categories" id="categories" required>
            <option value="232">Clube de Leitura</option>
            <option value="236">Clínica Virtual</option>
            <option value="228">Prática de Terapia Multidimensional</option>
            <option value="771">Workshop de Terapia Multidimensional</option>
            <option value="773">Workshop de Canalização</option>
            <option value="755">LIVE sobre a Terapia Multidimensional | Palestra</option>
        </select><br>
        <label for="zoom_required">Requer Zoom? <span class="requiredfield"> * </span></label>
        <select name="zoom_required" id="zoom_required" required>
            <option value="Yes">Sim</option>
            <option value="No">Não</option>
        </select><br>
        <label for="url">URL</label>
        <input type="url" name="url" id="url"><br>
        <p class="subtitle">Informe o link para a inscrião/informações da tua atividade para adquirir os ingressos, se necessário.</p>

        <input type="submit" value="Enviar Evento para Aprovação">
    </form>
    <style>
        /* set primary and secondary colors */
        :root {
            --primary-color: #4992ce;
            --secondary-color: #ffffff;
        }

        /* set form width and center it */
        .add_event_form {
            max-width: 800px;
            margin: 0 auto;
        }
        .sub_event_title{
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        /* style input fields */
        input[type="text"],
        input[type="email"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #4992ce;
            margin-bottom: 10px;
            box-sizing: border-box;
            background-color: var(--secondary-color);
            color: #333;
            font-size: 16px;
        }

        /* style required fields */
        .requiredfield {
            color: red;
        }

        /* style date and hour fields */
        .dates,
        .hours {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        /* style date and hour labels */
        .dates label,
        .hours label {
            width: 45%;
        }

        /* style submit button */
        input[type="submit"] {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            border: none;
            border-radius: 4px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* style submit button hover effect */
        input[type="submit"]:hover {
            background-color: #3e81b5;
        }

        /* style form title */
        .add_event_form h2 {
            font-size: 24px;
            margin-top: 0;
            color: var(--primary-color);
        }

        /* style form subtitle */
        .subtitle {
            font-size: 14px;
            color: #555;
            margin-top: 0;
        }

        /* style select fields */
        select {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #4992ce;;
            margin-bottom: 10px;
            box-sizing: border-box;
            background-color: var(--secondary-color);
            color: #333;
            font-size: 16px;
        }

        /* style date and hour input fields */
        input[type="date"],
        input[type="time"] {
            width: 45%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #4992ce;;
            margin-bottom: 10px;
            box-sizing: border-box;
            background-color: var(--secondary-color);
            color: #333;
            font-size: 16px;
            margin-right: 10px;
        }

        /* style URL input field */
        input[type="url"] {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #4992ce;;
            margin-bottom: 10px;
            box-sizing: border-box;
            background-color: var(--secondary-color);
            color: #333;
            font-size: 16px;
        }

        /* style form labels */
        label {
            display: block;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        /* style required fields */
        .requiredfield {
            color: red;
        }

        /* style form fieldset */
        fieldset {
            border: 1px solid #4992ce;
            margin: 0;
            padding: 0;
        }
    </style>

<?php


    return ob_get_clean();
}
add_shortcode('create_event', 'create_event');

function list_wc_order(){
    ob_start();
    global $wpdb;

    $url = "https://aptmd.org/wp-json/wc/v3/orders";

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode(WC_CONSUMER_KEY . ':' . WC_CONSUMER_SECRET)
        )
    ));

    $datas = json_decode(wp_remote_retrieve_body($response), true);
    if (!empty($datas)) {
        $i = 0;
        foreach ($datas as $data) :
            var_dump($data);
            if($i == 100) 
                break;
            else 
                echo "<br><br>";
            $i++;
        endforeach;
    }
    return ob_get_clean();
}
add_shortcode('list_wc_order', 'list_wc_order');

function add_socio(){
    ob_start();
    global $wpdb;
    $max_socio = intval($wpdb->get_var("SELECT MAX(CAST(meta_value as UNSIGNED)) FROM $wpdb->usermeta WHERE meta_key = 'Socio'"));
    echo $max_socio;
    $id = 2024;
    $socio = 691;
    /*if(isset($_POST['useremail'])){
        
        $user = get_user_by("email", $_POST['useremail']);
        $id = $user->ID;
        echo $_POST['useremail'];
      */  
        update_user_meta($id, 'Socio', $socio);
    //}
    
    ?>
    <form method="post">
        <input type="email" name="useremail">
        <input type="submit" name="submitemail">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('add_socio', 'add_socio');

function ap_event(){
    ob_start();

    global $wpdb;

    $publish = isset($_GET['publish']) ? $_GET['publish'] : 'none';
    if($publish != 'none'){
        $wpdb->update('wp_posts', array('post_status' => 'publish'), array('ID' => $publish));
        echo "<h1 style='color:green;'>Evento publicado com sucesso!</h1>";
    }

    $events = $wpdb->get_results("SELECT * FROM `wp_posts` where post_type like '%tribe_event%' and post_status != 'publish'");
    if(empty($events)){
        echo "<h1 style='color:green;'>No há eventos para aprovar!</h1>";
    }
    foreach($events as $event):
        $id = $event->ID;
        $meta = get_post_meta($event->ID);
        $_startdate = $meta['_EventStartDate'][0];
        $_enddate = $meta['_EventEndDate'][0];
        $_timezone = $meta['_EventTimezone'][0];
        $_author = get_user_by('id', $event->post_author)->display_name;
        $_email = get_user_by('id', $event->post_author)->user_email;
        $_name = $event->post_title;
        $_zoom = $meta['_Event_Zoom'][0];
        ?>
        <div class="_event">
            <div class="_event_id">#<?php echo $id; ?></div>
            <div class="_event_name"><?php echo $_name;?></div>
            <div class="_event_author"><?php echo $_author . " / ";?><a href="mailto:<?php echo $_email?>"><?php echo $_email?></a></div>
            <div class="_event_startdate"><?php echo $_startdate;?></div>
            <div class="_event_enddate"><?php echo $_enddate;?></div>
            <div class="_event_timezone"><?php echo $_timezone;?></div>
            <div class="_event_zoom"><?php 
            if($_zoom == "Yes") 
                echo "Necessita de Zoom!"; 
            else 
                echo "No Necessita de Zoom!";?></div>
            <div class="_event_approve">
                <a href="<?php echo add_query_arg(array("publish" => $event->ID))?>">Aprovar</a>
                <a href="<?php echo get_edit_post_link($id)?>">Editar</a>
            </div>
        </div>
        <?php
        endforeach;
        ?>
        <style>
            ._event{
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                border: 1px solid #4992ce;
                padding: 0% 2% 0% 2%;
                margin: 0 5% 2% 5%;
                min-height: 40%;
            }
            ._event > div{
                max-width: 16.66%;
                text-align: center;
                align-items: center;
                justify-content: center;
                min-height: 100%;
            }

            ._event_approve{
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                max-width: 16.66%;
            }
            
            @media only screen and (max-width: 720px) {
              ._event {
                flex-direction: column;
                padding: 1% 0 1% 0;
              }
              ._event div{
                max-width: 100%;
                text-align: center;
                align-items: center;
                justify-content: center;
                margin: 0 0 5% 0;
            }
            }

        </style>
        <?php
    return ob_get_clean();
}

add_shortcode('ap_event', 'ap_event');

function redirect_if_not_logged_in(){
    if(is_user_logged_in()){
        wp_redirect(home_url());
        exit;
    }else{
        wp_redirect(home_url("login"));
        exit;
    }
}

add_shortcode('redirect_if_not_logged_in', 'redirect_if_not_logged_in');

