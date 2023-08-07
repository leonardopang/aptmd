<?php
function relatorio()
{
    ob_start();
    $output_file = 'Rel - ' . date("Y-m-d") . '.csv';
    $gerado = false;

    if (isset($_POST['relEmail'])) {
        global $wpdb;

        $date_start = $_POST['startdate'] ? $_POST['startdate'] : false;
        echo $date_start . "<br>";
        $date_end = $_POST['enddate'] ? $_POST['enddate'] : false;
        echo $date_end . "<br>";

        // Set the output file name and path
        $output_file = 'Rel - ' . date("Y-m-d") . '.csv';

        // Open the output file in write mode
        $file = fopen($output_file, 'w');

        // Write the column headers to the output file
        fputcsv($file, array('Num. De Socio', 'ID', 'Nome', 'Email', 'Plano', 'Data Inicial', 'Data Final', 'Status', 'Origem', 'ID do Criador'));

        $plan_query = "SELECT * FROM `wp_posts` 
        WHERE `post_type` = 'ywcmbs-membership' 
        AND `post_status` = 'publish' 
        AND `guid` LIKE '%aptmd.org%'
        AND `post_title` NOT LIKE '%novo%'
        ORDER by post_date";

        if ($date_start) {
            $plan_query .= " AND `post_date` >= '{$date_start}'";
        }
        if ($date_end) {
            $plan_query .= " AND `post_date` <= '{$date_end}'";
        }
        $planos = $wpdb->get_results($plan_query);

        // Write the query results to the output file
        foreach ($planos as $plano) {
            $plan_id = $plano->ID;
            $author = $plano->post_author;
            $nome = $socio = $usuario = $email = null;
            if ($author === 0) {
                $origem = "ADMIN";
            } else {
                $origem = "Pagemento";
                $usuario = get_user_by('id', $author);
                $nome = $usuario->display_name;
                $email = $usuario->user_email;
                $socio = get_user_meta($author, 'Socio', true);
            }
            $metas = get_post_meta($plan_id);

            if($nome == null && $email == null){
                $author = intval($metas["_user_id"][0]);
                $usuario = get_user_by('id', $author);
                $nome = $usuario->display_name;
                $email = $usuario->user_email;
                $socio = get_user_meta($author, 'Socio', true);
            }

            fputcsv($file, array($socio, $author, $nome, $email, $metas["_title"][0], date('Y-m-d H:i:s', intval($metas["_start_date"][0])), date('Y-m-d H:i:s', intval($metas["_end_date"][0])), $metas["_status"][0], $origem));
            // echo "<br><br><br>".strpos($metas["_title"][0],"Novo");
            // var_dump(array($socio,$id, $nome, $email, $metas["_title"][0], date('Y-m-d H:i:s', intval($metas["_start_date"][0])), date('Y-m-d H:i:s', intval($metas["_end_date"][0])), $metas["_status"][0], $origem, $plan[0]->post_author));
        }
        // Close the output file
        fclose($file);
        echo "<h1>Relatório gerado!</h1>";
        $gerado = true;
    }
?>
    <h1 class="title">Gerar relatório</h1>
    <form action="" method="post">
        <label for="startdate">Data Inicial:</label>
        <input type="date" name="startdate" id="date"><br>
        <label for="enddate">Data Final:</label>
        <input type="date" name="enddate" id="date"><br><br>
        <input type="submit" class="Download" value="Gerar relatório Financeiro" name="relEmail">
    </form>
    <?php if ($gerado) : ?>
        <a href="<?php echo get_home_url() . '/' . $output_file ?>" download>Descarregar CSV</a>
    <?php endif; ?>

    <style>
        .Download {
            display: inline-block;
            padding: 10px 20px;
            background: #4992ce;
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            border-radius: 5px;
        }

        .title {
            text-align: center;
            color: #4992ce;
        }
    </style>
<?php

    return ob_get_clean();
}
add_shortcode('relatorio', 'relatorio');



function socios_tables()
{
    ob_start();
    global $wpdb;

   $search = $_GET['search'] ? $_GET['search'] : null;
    if($search){
        $query = "SELECT u.ID AS user_id, u.user_email, u.display_name, m.meta_value AS socio FROM wp_users u INNER JOIN wp_usermeta m ON u.ID = m.user_id AND m.meta_key = 'socio' AND (u.user_email LIKE '%$search%' OR u.display_name LIKE '%$search%' OR m.meta_value LIKE '%$search%') ORDER BY CAST(m.meta_value AS UNSIGNED) ASC, u.ID ASC ";
    }else{
        $query = "SELECT u.ID AS user_id, u.user_email, u.display_name, m.meta_value AS socio FROM wp_users u INNER JOIN wp_usermeta m ON u.ID = m.user_id AND m.meta_key = 'socio' ORDER BY CAST(m.meta_value AS UNSIGNED) ASC, u.ID ASC";
    }
    
    $usuarios = $wpdb->get_results($query);
?>
    <form action="" method="get">
        <input type="text" name="search" placeholder="Pesquisar">
        <input type="submit" value="Pesquisar">
    </form>
    <table class="socio-table">
        <thead class="socio-table-header">
            <tr class="socio-table-row-header">
                <th class="socio-table-info-header">ID</th>
                <th class="socio-table-info-header">Num. De Sócio</th>
                <th class="socio-table-info-header">Nome</th>
                <th class="socio-table-info-header">Email</th>
            </tr>
        </thead>
        <tbody class="socio-table-body">
            <?php foreach ($usuarios as $usuario) :
                $id = $usuario->user_id;
                $nome = $usuario->display_name;
                $email = $usuario->user_email;
                $socio = get_user_meta($id, 'Socio', true); ?>
                <tr class="socio-table-row-body">
                    <td class="socio-table-info-body"><?php echo $id; ?></td>
                    <td class="socio-table-info-body"><?php echo $socio; ?></td>
                    <td class="socio-table-info-body"><?php echo $nome; ?></td>
                    <td class="socio-table-info-body"><?php echo $email; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <style>
        .socio-table {
            width: 100%;
            border-collapse: collapse;
        }

        .socio-table-header {
            background-color: #4992ce;
            color: white;
        }

        .socio-table-row-header {
            text-align: center;
        }

        .socio-table-info-header {
            padding: 10px;
            border: 1px solid white;
        }

        .socio-table-row-body:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .socio-table-row-body:nth-child(even) {
            background-color: white;
        }

        .socio-table-info-body {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
    </style>
<?php
    return ob_get_clean();
}

add_shortcode('socios_tables', 'socios_tables');