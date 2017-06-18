<?php
/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 18.06.2017
 * Time: 12:14
 */

if($form) {
    ?><form action="<?php echo io_get_current_url(); ?>" method="post">
    <?php
} else {
    ?>
        <input type="submit" name="<?php echo User_Util::POST_ATTRIBUT_FRONTEND_SUBMIT; ?>" value="Speichern">
    </form>
    <?php
}