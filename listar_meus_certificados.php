<?php

require_once 'TmdCert.php';

function meus_certificados()
{
    ob_start();

    global $wpdb;

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_name = $current_user->display_name;
    $error = 0;
    $link_key = '';
    $mail = '';
    $aluno = '';

    if (isset($_GET['mail'])) {
        $mail = $_GET['mail'];
    }
    $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    if (!empty($search_term)) {
        $alunos_formados = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "aptmd_alunos_formados WHERE id_formador = $user_id and nome_aluno LIKE '%$search_term%' OR email_aluno LIKE '%$search_term%' or local LIKE '%$search_term%'");
    } else {
        $alunos_formados = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "aptmd_alunos_formados where id_formador = $user_id");
    }

    if ($mail) {
        $link_key = $mail;
        $aluno_formado = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "aptmd_alunos_formados WHERE `key` = '{$link_key}'");
        $aluno_formado = $aluno_formado[0];
        if ($aluno_formado->id_formador === $user_id) {
            $error = 1;
        } else {
            $formador2 = $aluno_formado->id_formador2 == 0 ? null : get_user_by('id', $aluno_formado->id_formador2)->display_name;
            $data_fim = $aluno_formado->data_fim;
            $data_inicio = $aluno_formado->data_inicio;
            $name = $aluno_formado->nome_aluno;
            $local = $aluno_formado->local;
            $carga_horaria = $aluno_formado->carga_horaria;
            $certificado = new TmdCertificado(
                $user_name,
                $formador2,
                $name,
                $data_inicio,
                $data_fim,
                $carga_horaria,
                $local
            );

            $name = $aluno_formado->nome_aluno;

            file_put_contents("Certificado TMD - " . $name . ".svg", $certificado->getCertificado());

            $imagick = new Imagick();

            $imagick->readImage("/home/aptmd.org/public_html/Certificado TMD - " . $name . ".svg");

            $imagick->setImageFormat('pdf');

            $imagick->writeImage("/home/aptmd.org/public_html/Certificado TMD - " . $name . ".pdf");
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $attachments = array(ABSPATH => "Certificado TMD - " . $name . ".pdf");
            $message = "Reenvio do teu certificado.";

            wp_mail($current_user->user_email, 'Certificado de TMD (Reenvio)', $message, $headers, $attachments);

            unlink("Certificado TMD - " . $name . ".svg");
            unlink("Certificado TMD - " . $name . ".pdf");
            echo "<h2>Certificado enviado para seu email</h2>";

        }
    }

    ?>
    <form method="get">
        <input type="text" name="search" id="search">
        <input type="submit" value="Localizar certificado">
    </form>
    <table class="meus_alunos">
        <thead class="table_header">
            <tr class="header_row">
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
            <?php foreach ($alunos_formados as $aluno): ?>
                <tr class="body_row">
                    <td>
                        <?php echo $aluno->nome_aluno; ?>
                    </td>
                    <td>
                        <?php echo $aluno->email_aluno; ?>
                    </td>
                    <td>
                        <?php echo $aluno->carga_horaria; ?>
                    </td>
                    <td>
                        <?php echo $aluno->data_inicio; ?>
                    </td>
                    <td>
                        <?php echo $aluno->data_fim; ?>
                    </td>
                    <td>
                        <?php echo $aluno->local; ?>
                    </td>
                    <td>
                        <a class="opcao" href="<?php echo add_query_arg(array("mail" => $aluno->key)) ?>">Enviar Para Teu
                            Email</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <style>
        h1.confirmacao {
            color: #4992ce;
            font-size: 2.5em;
            text-align: center;
            margin: 2em 0;
        }

        table.meus_alunos {
            width: 100%;
            border-collapse: collapse;
            margin: 2em 0;
        }

        table.meus_alunos thead {
            background-color: #4992ce;
            color: #ffffff;
        }

        table.meus_alunos th,
        table.meus_alunos td {
            padding: 1em;
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
add_shortcode('meus_certificados', 'meus_certificados');
?>