<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
?>
<!DOCTYPE html>
<html>
    <body>
        <form method="post" action="#" enctype="multipart/form-data">
            <input type="file" name="arquivo">
            <input type="submit" name="salvar" value="Salvar">
        </form>
        <?php
        $target_file = $target_dir . basename($_FILES["arquivo"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
        if (isset($_POST["salvar"])) {

            $id_medicos = array(3366, 3365, 3364, 3363, 3362, 3361, 3360, 3359, 3358, 3357, 3356, 3355, 3354, 3353, 3352, 3351, 3350, 3349, 3348, 3347, 3346, 3345, 3344, 3343, 3342, 3241, 3240, 3239, 3238, 3237, 3236, 3235, 3234, 3233, 3232, 3231, 3230, 3229, 3228, 3227, 3226, 3225, 3224, 3223, 3222, 3221, 3220, 3219, 3218, 3217, 3191, 3190, 3189, 3188, 3187, 3186, 3185, 3184, 3183, 3182, 3181, 3180, 3179, 3178, 3177, 3176, 3175, 3174, 3173, 3172, 3171, 3170, 3169, 3168, 3167, 3166, 3165, 3164, 3163, 3162, 3161, 3160, 3159, 3158, 3157, 3156, 3155, 3154, 3153, 3152, 3151, 3150, 3149, 3148, 3147, 3146, 3145, 3144, 3143, 3142, 3116, 3115, 3114, 3113, 3112, 3111, 3110, 3109, 3108, 3107, 3106, 3105, 3104, 3103, 3102, 3101, 3100, 3099, 3098, 3097, 3096, 3095, 3094, 3093, 3092, 3091, 3090, 3089, 3088, 3087, 3086, 3085, 3084, 3083, 3082, 3081, 3080, 3079, 3078, 3077, 3076, 3075, 3074, 3073, 3072, 3071, 3070, 3069, 3068, 3067, 3066, 3065, 3064, 3063, 3062, 3061, 3060, 3059, 3058, 3057, 3056, 3055, 3054, 3053, 3052, 3051, 3050, 3049, 3048, 3047, 3046, 3045, 3044, 3043, 3042, 3041, 3040, 3039, 3038, 3037, 3036, 3035, 3034, 3033, 3032, 3031, 3030, 3029, 3028, 3027, 3026, 3025, 3024, 3023, 3022, 3021, 3020, 3019, 3018, 3017, 2992, 2991, 2990, 2989, 2988);



            echo "<br><br>#{$_FILES["arquivo"]["name"]}<br><br>";

            $row = 1;
            $ids_sind = array();
            $cursos = array();
            $sindicatos = array();
            if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 99999, ";")) !== FALSE) {
                    $cpf = trim($data[2]);
                    $vr = trim($data[6]);
                    
                    $select = "SELECT id_clt,nome,id_projeto,id_curso FROM rh_clt WHERE cpf = '{$cpf}'";
                    $result = mysql_query($select);
//                    echo "<div class='text-danger text-bold'>$select</div>";
                    $num_rows = mysql_num_rows($result);
                    if ($row > 1) {

                        while ($x = mysql_fetch_assoc($result)) {

                            if (!in_array($x['id_curso'], $cursos) && $vr > 0) {
                                if (in_array($x['id_curso'], $id_medicos)) {
                                    // gravar vr no curso
                                    $qr = "SELECT nome FROM curso WHERE id_curso = {$x['id_curso']};";
                                    $l = mysql_fetch_assoc(mysql_query($qr));
                                    echo "UPDATE curso SET valor_refeicao = $vr WHERE nome LIKE '{$l['nome']}' AND campo3 IN(3,1); <br><br>";
                                    //echo 'Aloha<br>';
                                } else {
                                    // gravar vr no sindicato
                                    $query = "SELECT b.nome,b.id_sindicato FROM curso b WHERE b.id_curso = {$x['id_curso']}";
                                    $u = mysql_fetch_assoc(mysql_query($query));
                                    if (!in_array($u['id_sindicato'], $sindicatos)) {
                                        echo '# ';
                                        print_r($u);
                                        echo '<br>';
                                        echo "UPDATE rhsindicato a SET valor_refeicao = $vr WHERE (valor_refeicao IS NULL OR valor_refeicao = 0) AND a.id_sindicato = {$u['id_sindicato']}; <br><br>";
                                        $sindicatos[] = $u['id_sindicato'];
                                    }
                                }
                                $cursos[] = $x['id_curso'];
                            }
                        }
                    }
                    $row++;
                }
                fclose($handle);
            } else {
                echo 'erro!!';
            }
            echo "<br><br>\n";
        }
        ?>
    </body>
</html>
