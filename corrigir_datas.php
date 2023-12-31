<?php
function corrigir_datas()
{
    ob_start();

    global $wpdb;

    $encomendas = $wpdb->get_results("SELECT * FROM {$wpdb->posts} 
    WHERE `post_type` = 'ywcmbs-membership' 
    AND `post_status` = 'publish' 
    AND `guid` LIKE '%aptmd.org%'
    AND `post_title` NOT LIKE '%novo%'");

    foreach($encomendas as $plan){
        $id = $plan->ID;
        $metas = get_post_meta($id);
        $dataI = date('Y-m-d H:i:s', $metas["_start_date"][0]);
        $dateBroken = explode('-', explode(' ', $dataI)[0]);
        $mes = $dateBroken[1];
        $title = $plan->post_title;
        $dataFinal = '';
        if(strpos($title, '1 Semestre')){
            if(intval($mes) <= 5){
                $dataFinal = $dateBroken[0].'-06-30 19:59:00';
            }else{
                $dataFinal = $dateBroken[0].'-12-31 19:59:00';
            }
            $dataFinal = strtotime($dataFinal);
            update_post_meta($id, '_end_date', $dataFinal);
        }else {
            if(intval($mes) <= 3){
                $dataFinal = $dateBroken[0].'-12-31 19:59:00';
            }else{
                $dataFinal = (intval($dateBroken[0]) + 1).'-06-30 19:59:00';
            }
            $dataFinal = strtotime($dataFinal);
            update_post_meta($id, '_end_date', $dataFinal);
        }       
    }
    return ob_get_clean();
}
add_shortcode('corrigir_datas', 'corrigir_datas');
