<?php

function save_ticket()
{
    $current_user_id = null;
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
    } else {
        $current_user_id = 0;
    }

    global $wpdb;

    if (isset($_POST['VP_ticket_type']) && isset($_POST['VP_ticket_content'])) {
        $type = $_POST['VP_ticket_type'];
        $content = $_POST['VP_ticket_content'];
        $email = $_POST['VP_ticket_email'];

        $post_id = wp_insert_post(array(
            'post_title' => 'Chamado #' . time(),
            'post_status' => 'publish',
            'post_type' => 'VP_Ticket',
        ));

        update_post_meta($post_id, '_VPTicketType', $type);
        update_post_meta($post_id, '_VPTicketStatus', '1');
        update_post_meta($post_id, '_VPTicketEmail', $email);

        $wpdb->insert('a_vp_tickets', array(
            'ticket_id' => $post_id,
            'msg_author' => $current_user_id,
            'msg_content' => $content,
            'msg_order' => 1,
            'msg_date' => date('Y-m-d H:i:s')
        ));
        wp_redirect('https://aptmd.org/aptmd-suporte/?success=true');
        exit;
    }
}

add_action('admin_post_VP_TicketForm', 'save_ticket');
add_action('admin_post_nopriv_VP_TicketForm', 'save_ticket');

function add_vp_ticket()
{
    // Clear cache.
    // Also preload the cache if the Preload is enabled.
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }

    // Clear minified CSS and JavaScript files.
    if (function_exists('rocket_clean_minify')) {
        rocket_clean_minify();
    }
    ob_start();
    $TICKETTYPE = array(
        'TECNICO' => 'Tecnico',
        'EDUCACIONAL' => 'Educacional',
        'FINANCEIRO' => 'Financeiro',
        'PERSONALIZADO' => 'Personalizado'
    );


    $current_user_id = null;
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
    } else {
        $current_user_id = 0;
    }

    global $wpdb;

    if (isset($_POST['VP_ticket_type']) && isset($_POST['VP_ticket_content'])) {
        $type = $_POST['VP_ticket_type'];
        $content = $_POST['VP_ticket_content'];
        $email = $_POST['VP_ticket_email'];

        $post_id = wp_insert_post(array(
            'post_title' => 'Chamado #' . time(),
            'post_status' => 'publish',
            'post_type' => 'VP_Ticket',
        ));

        update_post_meta($post_id, '_VPTicketType', $type);
        update_post_meta($post_id, '_VPTicketStatus', '1');
        update_post_meta($post_id, '_VPTicketEmail', $email);

        $wpdb->insert('a_vp_tickets', array(
            'ticket_id' => $post_id,
            'msg_author' => $current_user_id,
            'msg_content' => $content,
            'msg_order' => 1,
            'msg_date' => date('Y-m-d H:i:s')
        ));
        wp_redirect('https://aptmd.org/aptmd-suporte/?success=true');
        exit;
    }

?>
    <div class="VP_ticket_wrap">
        <?php if (isset($_GET['success'])) {
            echo '<div class="VP_ticket_wrap">
        <h1 class="VP_ticket_blue">Chamado aberto com sucesso!</h1>
        <h2 class="VP_ticket_blue">Aguarde nosso retorno.</p>
        </div>';
        } ?>
        <form method="post" class="VP_ticket_form">
            <input type="hidden" name="action" value="VP_TicketForm" />
            <h1 class="VP_ticket_blue">APTMD Suporte</h1>
            <h2 class="VP_ticket_blue">Por favor, preencha o formulário abaixo para abrir um chamado.</p>

                <div>
                    <input type="hidden" name="VP_userid" value="<?php echo $current_user_id; ?>">
                </div>
                <div class="VP_ticket_section">
                    <label class="VP_ticket_label" for="VP_ticket_title">Email:</label>
                    <input placeholder="Teu Email para avisarmos quando teu chamado houver sido respondido" name="VP_ticket_email" type="text" class="VP_ticket_input" value="<?php echo $current_user->user_email; ?>" required />
                </div>

                <div class="VP_ticket_section">
                    <label class="VP_ticket_label" for="VP_ticket_title">Descrição:</label>
                    <textarea placeholder="Descreva teu problema em detalhes aqui..." name="VP_ticket_content" class="VP_ticket_input VP_textarea" rows="5" cols="30" required></textarea>
                </div>
                <div class="VP_ticket_section">
                    <label class="VP_ticket_label" for="VP_ticket_title">Tipo:</label>
                    <select name="VP_ticket_type" class="VP_ticket_input" required>
                        <?php foreach ($TICKETTYPE as $key => $TICKET) : ?>
                            <option value="<?php echo $key ?>"><?php echo $TICKET ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="submit" name="submit" id="submit" class="VP_ticket_submit" value="Abrir Chamado">
        </form>
    </div>
    <style>
        .VP_ticket_form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 1px solid #4992ce;
            border-radius: 15px;
            padding: 5% 10% 10% 5%;
            background-color: white;
            color: #4992ce;
        }

        .VP_ticket_form input,
        .VP_ticket_form textarea,
        .VP_ticket_form select {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #4992ce;
            border-radius: 5px;
            outline: none;
        }

        .VP_ticket_label {
            padding: 0;
            color: #4992ce;
        }

        .VP_ticket_section {
            width: 100%;
            margin: 5% 0;
        }

        .VP_ticket_blue {
            color: #4992ce;
        }
    </style>
<?php
    return ob_get_clean();
}

add_shortcode('add_vp_ticket', 'add_vp_ticket');

function admin_vp_tickets()
{

    require('wp-load.php');

    // Clear cache.
    // Also preload the cache if the Preload is enabled.
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }

    // Clear minified CSS and JavaScript files.
    if (function_exists('rocket_clean_minify')) {
        rocket_clean_minify();
    }
    ob_start();
    global $wpdb;

    if (isset($_GET['delete'])) {
        $ticket_id = $_GET['delete'];
        wp_delete_post($ticket_id, true);
        $wpdb->delete('a_vp_tickets', array('ticket_id' => $ticket_id));
    }

    if (isset($_GET['update'])) {
        $ticket_id = $_GET['update'];
        $ticket_status = 0;
        update_post_meta($ticket_id, '_VPTicketStatus', $ticket_status);
    }

    if(is_admin()){
        $tickets = get_posts(array(
            'post_type' => 'VP_Ticket',
            'post_status' => 'publish',
            'numberposts' => -1
        ));
    }else {
        $tickets = get_posts(array(
            'post_type' => 'VP_Ticket',
            'post_status' => 'publish',
            'numberposts' => -1,
            'author' => get_current_user_id()
        ));
    }

?>
    <table class="_vp_tickets_tables">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titulo</th>
                <th>Tipo</th>
                <th>Autor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket) :
                $author = get_user_by('id', $ticket->post_author);
            ?>
                <tr>
                    <td>#<?php echo $ticket->ID; ?></td>
                    <td><?php echo $ticket->post_title; ?></td>
                    <td><?php echo get_post_meta($ticket->ID, '_VPTicketType', true); ?></td>
                    <td><?php echo $author->display_name . "/" . $author->user_email; ?></td>
                    <td><?php echo get_post_meta($ticket->ID, '_VPTicketStatus', true) == 1 ? 'Aberto' : 'Fechado'; ?></td>
                    <td>
                        <?php if (get_post_meta($ticket->ID, '_VPTicketStatus', true) == 1) : ?>
                            <a href="<?php echo "https://aptmd.org/tickets/?chat=" . $ticket->ID; ?>">Responder</a>
                        <?php endif; ?>
                        <?php if (get_post_meta($ticket->ID, '_VPTicketStatus', true) == 1) : ?>
                            <a href="<?php echo add_query_arg(array("update" => $ticket->ID)); ?>">Fechar</a>
                        <?php endif; ?>
                        <a href="<?php echo add_query_arg(array("delete" => $ticket->ID)); ?>">Deletar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <style>
        /* Table Styles */
        ._vp_tickets_tables {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        /* Table Header Styles */
        ._vp_tickets_tables thead {
            background-color: #4992ce;
            color: #fff;
        }

        ._vp_tickets_tables th {
            font-weight: bold;
            padding: 10px;
            text-align: center;
        }

        /* Table Body Styles */
        ._vp_tickets_tables tbody tr {
            background-color: #f5f5f5;
        }

        ._vp_tickets_tables tbody tr:nth-child(even) {
            background-color: #fff;
        }

        ._vp_tickets_tables tbody tr:hover {
            background-color: #49abce;
            color: #fff;
        }

        ._vp_tickets_tables td {
            padding: 10px;
            text-align: center;
        }

        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
            ._vp_tickets_tables {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
    <?php

    return ob_get_clean();
}
add_shortcode('admin_vp_tickets', 'admin_vp_tickets');

function vp_tickets_chat()
{
    require('wp-load.php');

    // Clear cache.
    // Also preload the cache if the Preload is enabled.
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }

    // Clear minified CSS and JavaScript files.
    if (function_exists('rocket_clean_minify')) {
        rocket_clean_minify();
    }
    ob_start();
    global $wpdb;

    if (isset($_POST['VP_submit'])) {
        global $wpdb;
        $ticket_id = $_POST['VP_ticket_id'];
        $msg_author = $_POST['VP_msg_author'];
        $msg_content = $_POST['VP_msg_content'];
        $msg_order = $_POST['VP_msg_order'];

        $wpdb->insert('a_vp_tickets', array(
            'ticket_id' => $ticket_id,
            'msg_author' => $msg_author,
            'msg_content' => $msg_content,
            'msg_order' => $msg_order,
            'msg_date' => date('Y-m-d H:i:s')
        ));
        $ticket = get_post($ticket_id);
        $email = get_user_by('id', $ticket->post_author)->user_email;

        $admin = user_can($msg_author, 'administrator') ? true : false;
        if ($admin) {
            wp_mail($email, 'Nova Mensagem em teu ticket de suporte', 'Tu tens uma nova mensagem em teu ticket aberto no site da APTMD. Acesse o link abaixo para ver a mensagem: https://aptmd.org/tickets/?chat=' . $ticket_id . '');
        }

        wp_redirect("https://aptmd.org/tickets/?chat=$ticket_id");
        exit;
    }

    $ticket_id = $_GET['chat'] ? $_GET['chat'] : 0;
    if ($ticket_id == 0) :
        return;
    else :
        $current_user_id = get_current_user_id();
        if(!is_admin() && $ticket->post_author != $current_user_id) {
            return;
        }
        $ticket_msgs = $wpdb->get_results("SELECT * FROM a_vp_tickets WHERE ticket_id = $ticket_id");
    ?><div class="VP_chat"><?php
                            foreach ($ticket_msgs as $msg) :
                                $msg_author = get_user_by('id', $msg->msg_author)->display_name;
                                $msg_content = $msg->msg_content;
                                $msg_date = $msg->msg_date;
                                $msg_order = $msg->msg_order;
                                $admin = user_can($msg->msg_author, 'administrator') ? true : false;
                                if (intval($msg_order) == 1)
                                    $admin = false;

                            ?>
                <div class="VP_ticket_msg <?php if ($admin) echo " VP_admin"; ?>">
                    <div class="VP_ticket_msg_content">
                        <?php echo $msg_content; ?>
                    </div>
                    <div class="VP_ticket_footer">
                        <div class="VP_msg_author">
                            <?php $msg_author = $admin ? "ADMIN" : $msg_author;
                                echo $msg_author; ?>
                        </div>
                        <div class="VP_msg_date"><?php echo $msg_date; ?></div>
                    </div>
                </div>
            <?php
            endforeach;
            // echo esc_attr(admin_url('admin-post.php'));
            ?>
        </div>

        <form method="post">
            <input type="hidden" name="action" value="VP_postmsg" />
            <input type="hidden" name="VP_ticket_id" value="<?php echo $ticket_id; ?>">
            <input type="hidden" name="VP_msg_author" value="<?php echo $current_user_id; ?>">
            <input type="hidden" name="VP_msg_order" value="<?php intval($wpdb->get_results("SELECT MAX(msg_order) FROM a_vp_tickets WHERE id = {$ticket_id}")[0]) + 1 ?>">
            <input type="text" name="VP_msg_content" class="VP_inputfield">
            <input name="VP_submit" type="submit" value="Enviar" class="VP_submit">
        </form>
        <style>
            /* Input field Styles */
            .VP_inputfield {
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 8px 12px;
                font-size: 16px;
                line-height: 1.5;
                width: 88%;
                margin-right: 10px;
            }

            /* Submit button Styles */
            .VP_submit {
                background-color: #4992ce;
                color: #fff;
                border: none;
                border-radius: 4px;
                padding: 10px 16px;
                font-size: 16px;
                line-height: 1.5;
                cursor: pointer;
                width: 10%;
            }

            .VP_submit:hover {
                background-color: #2f649e;
            }


            .VP_ticket_msg {
                border: 1px #2ca0b8 solid;
                background-color: #2ca0b8;
                color: #fff;
                border-radius: 25px;
                max-width: 40%;
                min-width: 40%;
                padding: 10px 10px 5px 10px;
                margin: 0px 0px 10px 0px;
            }

            .VP_admin {
                background-color: #fff;
                color: #2ca0b8;
                border-radius: 25px;
                max-width: 40%;
                min-width: 40%;
                margin-left: 60%;
            }

            .VP_ticket_footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .VP_msg_author {
                font-weight: bold;
                font-size: 12px;
            }

            .VP_msg_date {
                font-size: 10px;
            }

            @media only screen and (max-width: 600px) {
                .VP_inputfield {
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    padding: 8px 12px;
                    font-size: 16px;
                    line-height: 1.5;
                    width: 68%;
                    margin-right: 10px;
                }

                /* Submit button Styles */
                .VP_submit {
                    background-color: #4992ce;
                    color: #fff;
                    border: none;
                    border-radius: 4px;
                    padding: 10px 16px;
                    font-size: 16px;
                    line-height: 1.5;
                    cursor: pointer;
                    width: 30%;
                }
            }
        </style>
<?php
    endif;
    return ob_get_clean();
}
add_shortcode('vp_tickets_chat', 'vp_tickets_chat');

function post_msg()
{
    global $wpdb;
    $ticket_id = $_POST['VP_ticket_id'];
    $msg_author = $_POST['VP_msg_author'];
    $msg_content = $_POST['VP_msg_content'];
    $msg_order = $_POST['VP_msg_order'];

    $wpdb->insert('a_vp_tickets', array(
        'ticket_id' => $ticket_id,
        'msg_author' => $msg_author,
        'msg_content' => $msg_content,
        'msg_order' => $msg_order,
        'msg_date' => date('Y-m-d H:i:s')
    ));
    $ticket = get_post($ticket_id);
    $email = get_user_by('id', $ticket->post_author)->user_email;

    $admin = user_can($msg_author, 'administrator') ? true : false;
    if ($admin) {
        wp_mail($email, 'Nova Mensagem em teu ticket de suporte', 'Tu tens uma nova mensagem em teu ticket aberto no site da APTMD. Acesse o link abaixo para ver a mensagem: https://aptmd.org/tickets/?chat=' . $ticket_id . '');
    }

    wp_redirect("https://aptmd.org/tickets/?chat=$ticket_id");
    exit;
}
add_action('admin_post_nopriv_VP_postmsg', 'post_msg');
add_action('admin_post_VP_postmsg', 'post_msg');
