<?php

$current_user_id = null;
if(is_user_logged_in()){
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;
}else{
    $id = 0;
}

global $wpdb;

if(isset($_POST['VP_ticket_type']) && isset($_POST['VP_ticket_content'])){
    $id = $_POST['VP_userid'];
    $type = $_POST['VP_ticket_type'];
    $content = $_POST['VP_ticket_content'];
    $email = $_POST['VP_ticket_email'];

    $post_id = wp_insert_post(array(
        'post_title' => 'Chamado #'.time(),
        'post_status' => 'publish',
        'post_author' => $id,
        'post_type' => 'VP_Ticket',
    ));

    update_post_meta($post_id, '_VPTicketType', $type);
    update_post_meta($post_id, '_VPTicketStatus', true);
    update_post_meta($post_id, '_VPTicketEmail', $email);
    update_post_meta($post_id, '_VPTicketId', $current_user_id);

    $wpdb->insert($wpdb->prefix.'a_vp_tickets', array(
        'ticket_id' => $post_id,
        'msg_author' => $current_user_id,
        'msg_content' => $content,
        'msg_order' => 0,
        'msg_date' => date('Y-m-d H:i:s')
    ));
    $wpdb->insert($wpdb->prefix."a_vp_user_tickets", array(
        'ticket_id' => $post_id,
        'user_id' => $id
    ));

    echo "teste";
    //wp_safe_redirect('aptmd-suporte/?ticketid='.$post_id);
    exit;
}