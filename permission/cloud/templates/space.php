<?php
/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 30.12.2016
 * Time: 15:27
 */
?>

<h1>Eigener Cloud-Space</h1>

Cloud-Space kann für die eigene Arbeit sehr wichtig sein. Die Cloud bildet dabei die gemeinsame Arbeitsplattform für die Gruppe. Es ermöglicht die Ausarbeitung von Projekten, sowie das Teilen von Dateien an andere, aussenstehende.<br><br>

<?php

    if(!$model->hasSpace) {
?>

        Unsere Cloud ist durchstrukturiert und geordnet. Jeder Ordner erfüllt einen eigenen Zweck. Solltest du einen Cloud-Ordner für deine Zwecke benötigen, drücke folgenden Button:

        <form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
            <?php
                wp_nonce_field(self::NONCE, self::POST_NONCE);
            ?>
            <input type="submit" name="<?php echo cloud_view::POST_SUBMIT; ?>" value="Cloud-Space beantragen">
        </form><br><br>

        Mit Betätigung des Buttons beantragst du einen Cloud-Space, der von der Webmasterei anschließend erstellt werden muss. Das kann ein paar Tage dauern.

<?php
    } else {
?>

        Du besitzt einen Cloud-Ordner, denn du beim Login in die Cloud sehen und nutzen kannst. Um anderen den Zugriff auf deinen Cloud-Ordner zu ermöglichen, gehe zur <a href="wp-admin/post.php?post=<?php

        echo get_page_by_title("Cloud " . (new User(get_current_user_id()))->user_login)->ID;

        ?>&action=edit">Gruppenverwaltung</a> und füge die entsprechenden Nutzer*innen der Gruppe hinzu.<br><br>

        Beachte bitte: Die neuen Nutzer*innen müssen bereits im IGELoffice registriert sein.

<?php
    }

?>
