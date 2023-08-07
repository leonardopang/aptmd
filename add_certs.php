<?php

function add_certs(){
    ob_start();

    if(isset($_GET['certtype']) && isset($_GET['email']) && isset($_GET['quantidade'])){
        $certtype = $_GET['certtype'];
        $email = $_GET['email'];
        $quantidade = intval($_GET['quantidade']);
        $quantidade = $quantidade < 0 ? -1*$quantidade : $quantidade;

        $user = get_user_by('email', $email);
        $user_id = $user->ID;

        if($certtype == 'certificados'){
            $certtype = 'certificados';
        } else if($certtype == 'canalizacao'){
            $certtype = 'canalizacao';
        }

        $certs = intval(get_user_meta($user_id, $certtype, true));
        $certs = $certs + $quantidade;
        update_user_meta($user_id, $certtype, $certs);

        echo "<h1>" . $quantidade ." Adicionado com sucesso!</h1>";
    }
    if(isset($_GET['removercerttype']) && isset($_GET['removeremail']) && isset($_GET['removerquantidade'])){
        $certtype = $_GET['removercerttype'];
        $email = $_GET['removeremail'];
        $quantidade = intval($_GET['removerquantidade']);
        $quantidade = $quantidade > 0 ? -1*$quantidade : $quantidade;

        $user = get_user_by('email', $email);
        $user_id = $user->ID;

        if($certtype == 'certificados'){
            $certtype = 'certificados';
        } else if($certtype == 'canalizacao'){
            $certtype = 'canalizacao';
        }

        $certs = intval(get_user_meta($user_id, $certtype, true));
        $certs = $certs + $quantidade;
        update_user_meta($user_id, $certtype, $certs);

        echo "<h1>".$quantidade ." certificado(s) removido(s) com sucesso!</h1>";
    }

    ?>
    <h1>Adicionar Certiticados</h1>
    <form action="" method="get">
        <select name="certtype" id="certtype">
            <option value="certificados">TMD</option>
            <option value="canalizacao">Canalização</option>
        </select>
        <input type="email" name="email" id="certemail" placeholder="Email do Sócio">
        <input type="number" name="quantidade" id="certquant" placeholder="Quantidade de Certificados">
        <input type="submit" value="Adicionar">
    </form>
    <br/>
    <br/>
    <h1>Remover Certiticados</h1>
    <form action="" method="get">
        <select name="removercerttype" id="certtype">
            <option value="certificados">TMD</option>
            <option value="canalizacao">Canalização</option>
        </select>
        <input type="email" name="removeremail" id="certemail" placeholder="Email do Sócio">
        <input type="number" name="removerquantidade" id="certquant" placeholder="Quantidade de Certificados">
        <input type="submit" value="Remover">
    </form>
    <style>
        form {
  display: flex;
  flex-direction: column;
  align-items: center;
}

select, input {
  width: 100%;
  padding: 10px;
  margin: 5px 0;
  border-radius: 5px;
  border: 1px solid #ccc;
}

select:focus, input:focus {
  outline: none;
  border-color: #4992ce;
}

    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('add_certs', 'add_certs');

function APTMD_Add_Certificados()
{
    ob_start();
    ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="APTMD_Add_Certificados">
        <select name="certtype" id="certtype">
            <option value="certificados">TMD</option>
            <option value="canalizacao">Canalização</option>
        </select>
        <input type="email" name="email" id="certemail" placeholder="Email do Sócio">
        <input type="number" name="quantidade" id="certquant" placeholder="Quantidade de Certificados">
        <input type="submit" value="Adicionar">
    </form>
    <?php
    return ob_get_clean();
}
function getUser($param)
    {
        if (is_email($param)) {
            return get_user_by('email', $param);
        } else if (is_numeric($param)) {
            return get_user_by('id', $param);
        }
        return false;
    }

add_shortcode('APTMD_Add_Certificados', 'APTMD_Add_Certificados');

function handleAddCertificados()
{
    $user = getUser($_POST['email']);
    if ($user) {
        $certtype = $_POST['certtype'];
        $quantidade = $_POST['quantidade'];
        $certificados = get_user_meta($user->ID, $certtype, true);
        if ($certificados) {
            $certificados += $quantidade;
        } else {
            $certificados = $quantidade;
        }
        update_user_meta($user->ID, $certtype, $certificados);
        wp_redirect('https://www.aptmd.org/minha-conta/add-cert/'.'?success=true');
    } 
    wp_redirect('https://www.aptmd.org/minha-conta/add-cert/'.'?success=false');
}
add_action('admin_post_APTMD_Add_Certificados', 'handleAddCertificados');
add_action('admin_post_nopriv_APTMD_Add_Certificados', 'handleAddCertificados');