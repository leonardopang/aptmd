<?php

require 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;

require_once 'CanalCert.php';
function meus_canalizacao(){
    ob_start();

    global $wpdb;

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_name = get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true);
    $error = 0;
    $link_key = '';
    $mail = '';
    $aluno = '';

    if(isset($_GET['mail'])){
        $mail = $_GET['mail'];
        $aluno = $wpdb->get_results("SELECT * FROM `wp_aptmd_formador_formados` WHERE `key` = '$mail'")[0];
        $formador1 = get_user_by('id', $aluno->id_formador);
        $formador1 = $formador1->display_name;
        $formador2 = $aluno->id_formador2 != 0 ? get_user_by('id', $aluno->id_formador2)->display_name : null;

        $pais = explode('/', $aluno->local)[0];
        $cidade = explode('/', $aluno->local)[1];
        $espaco = explode('/', $aluno->local)[2];

        $certificado = new CanalCert(
            $formador1,
            $formador2,
            $aluno->nome_aluno,
            $aluno->email_aluno,
            $aluno->nascimento,
            $aluno->data_inicio,
            $aluno->data_fim,
            $aluno->carga_horaria,
            $cidade,
            $pais,
            $espaco
        );
        $name = $aluno->nome_aluno;

        file_put_contents("Certificado Canalizacao - " . $name . ".svg", $certificado->get_certificado());
        
        $imagick = new Imagick();

        $imagick->readImage("/home/aptmd.org/public_html/Certificado Canalizacao - " . $name . ".svg");

        $imagick->setImageFormat('pdf');

        $imagick->writeImage("/home/aptmd.org/public_html/Certificado Canalizacao - " . $name . ".pdf");
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $attachments = array(ABSPATH => "Certificado Canalizacao - " . $name . ".pdf");
        $message = "Reenvio do teu certificado.";
      
        wp_mail($current_user->user_email, 'Certificado de Canalização (Reenvio)', $message, $headers, $attachments);

        unlink("Certificado Canalizacao - " . $name . ".svg");
        unlink("Certificado Canalizacao - " . $name . ".pdf");
        echo "<h2>Certificado enviado para seu email</h2>";
        remove_query_arg('mail');
    }


    $alunos_formados = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."aptmd_formador_formados WHERE id_formador = $user_id");
    ?>
        <table class="meus_alunos">
            <thead class="table_header">
                <tr class="header_row">
                    <th>Formador</th>
                    <th>Formador Parceiro</th>
                    <th>Nome Formando</th>
                    <th>Email</th>
                    <th>Carga Horária</th>
                    <th>Data de Incio</th>
                    <th>Data de Fim</th>
                    <th>Localização</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody class="table_body">
                <?php foreach($alunos_formados as $aluno):
                    $formador2 = get_user_by('id', $aluno->id_formador2);
                    ?>
                    <tr class="body_row">
                        <td><?php echo $current_user->first_name . ' ' . $current_user->last_name;?></td>
                        <td><?php echo $formador2->first_name . ' ' . $formador2->last_name;?></td>
                        <td><?php echo $aluno->nome_aluno;?></td>
                        <td><?php echo $aluno->email_aluno;?></td>
                        <td><?php echo $aluno->carga_horaria;?></td>
                        <td><?php echo $aluno->data_inicio;?></td>
                        <td><?php echo $aluno->data_fim;?></td>
                        <td><?php $local = explode('/', $aluno->local); 
                                foreach($local as $loc){
                                    echo $loc . '/<br>';
                                }?></td>
                        <td>
                            <a class="opcao" href="<?php echo add_query_arg(array("mail" => $aluno->key))?>">Enviar Para Seu Email</a>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <style>
        h1.confirmacao {
            color: #4992ce;
            font-size: 20px;
            text-align: center;
            margin: 15px 0;
        }

        table.meus_alunos {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            overflow-x: auto;
        }

        table.meus_alunos thead {
            background-color: #4992ce;
            color: #ffffff;
        }

        table.meus_alunos th,
        table.meus_alunos td {
            padding: 5px;
            border: 1px solid #ccc;
        }

        table.meus_alunos tbody tr:hover {
            background-color: #f2f2f2;
        }

        a.opcao {
            display: block;
            width: 100%;
            text-align: center;
            color: #4992ce;
            text-decoration: none;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('meus_canalizacao', 'meus_canalizacao');
?>