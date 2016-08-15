Der Bundesverband der GR&Uuml;NEN JUGEND bietet jeder Basisgruppe und jedem Landesverband die M&ouml;glichkeit an, eine WordPress-Seite im System des Bundesverbandes anzulegen. Dabei ist es f&uuml;r die jeweiligen Gruppen nicht mehr notwendig, auf&auml;ndig einen Webhoster zu suchen und eine WordPress-Instanz zu installieren.
<br><br>

Wir m&ouml;chten damit unseren Gremien einen m&ouml;glichst einfachen Service anbieten, der es ihnen erm&ouml;glicht schnell Online zu gehen und f&uuml;r die jeweiligen Positionen zu werben. Dabei &uuml;bernehmen wir jeden Aufwand in der Verwaltung und der Betreuung der Infrastruktur. Die Gruppe kann sich ganz auf das Wesentliche konzentrieren: Politik und &ouml;ffentlichkeitsarbeit.
<br><br>

Auf unseren WordPress-Instanzen sind diverse Templates vorinstalliert. Dabei k&ouml;nnt ihr nat&uuml;rlich auch auf das Template des Corporate Designs zur&uuml;ckgreifen und es nach euren W&uuml;nschen modifizieren. Ebenso gibt es eine Auswahl an Erweiterungen, die in eurer WordPress-Installation genutzt werden. Sollte euch dabei etwas fehlen, wende dich bitte an die Webmaster.
<br><br>

Bei der neu erstellten WordPress-Seite ist nur der Account der Gruppe Administrator_in der neuen Seite. Es ist aber auch m&ouml;glich, weitere Menschen als Autor_innen, Editor_innen, Mitarbeiter_innen oder Abonnent_innen einzutragen. All jene Personen m&uuml;ssen im IGELoffice registriert sein. Nur dann ist es dem Gruppen-Account m&ouml;glich, im Backend des IGELoffices unter Gruppe die entsprechenden Personen in der jeweiligen Gruppe hinzuzuf&uuml;gen.
<br><br>

<hr><br><br>

<div id="wordpress_create_submitted"<?php echo $wordpress_create_submitted_css; ?>>
    Deine WordPress-Seite wurde erfolgreich erstellt.
</div>
<div id="wordpress_delete_submitted"<?php echo $wordpress_delete_submitted_css; ?>>
    Deine WordPress-Seite wurde erfolgreich gel&ouml;scht.
</div>
<div id="wordpress_create"<?php echo $wordpress_create_css; ?>>
    Deine WordPress-Seite wird unter der Domain <b><?php echo Domain_Control::prepareDomain($user->user_login); ?>
        .gruene-jugend.de</b> erstellt und aufrufbar sein.<br><br>

    Es ist dir sp&auml;ter m&ouml;glich, eine andere Domain f&uuml;r diese WordPress-Seite auszuw&auml;hlen.<br><br>

    <form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
        <input type="submit" name="<?php echo WordPress_Util::POST_ATTRIBUT_CREATE_SUBMIT; ?>"
               value="WordPress-Seite erstellen">
    </form>

    <hr>
    <br><br>
</div>
<div id="wordpress_create_fail"<?php echo $wordpress_create_fail_css; ?>>
    Es ist f&uuml;r dich nicht m&ouml;glich eine WordPress-Seite zu erstellen, da die Subdomain
    <b><?php echo Domain_Control::prepareDomain($user->user_login); ?>.gruene-jugend.de</b> bereits als Weiterleitung
    benutzt wird.<br><br>

    Bitte l&ouml;sche erst die Weiterleitung, bevor du eine WordPress-Seite erstellen kannst.<br><br>
    <hr>
    <br><br>
</div>
<div id="wordpress_edit"<?php echo $wordpress_edit_css; ?>>
    Deine WordPress-Seite: <b><?php echo Domain_Control::prepareDomain($user->user_login); ?>.gruene-jugend.de</b><br>
    Admin-Bereich: <b><?php echo Domain_Control::prepareDomain($user->user_login); ?>
        .gruene-jugend.de/wp-admin</b><br><br>

    Gruppen, in denen du Benutzer_innen f&uuml;r deine WordPress-Seite hinzuf&uuml;gen kannst:<br><br>

    <ul>
        <li>
            <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $mitarbeiter_innen_id; ?>&action=edit">Mitarbeiter_innen</a>
        </li>
        <li>
            <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $redakteur_innen_id; ?>&action=edit">Redakteur_innen</a>
        </li>
        <li><a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $autor_innen_id; ?>&action=edit">Autor_innen</a>
        </li>
        <li>
            <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $abonnent_innen_id; ?>&action=edit">Abonnent_innen</a>
        </li>
    </ul>
    <hr>
    <br><br>
</div>
<div id="wordpress_delete"<?php echo $wordpress_delete_css; ?>>
    Wenn du deine WordPress-Seite bei der GR&uuml;NEN JUGEND l&ouml;schen m&ouml;chtest, klicke auf folgenden Button.
    Bitte sei dir dessen vorher ganz sicher.<br><br>

    <form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
        <input type="submit" name="<?php echo WordPress_Util::POST_ATTRIBUT_DELETE_SUBMIT; ?>"
               value="WordPress-Seite l&ouml;schen">
    </form>
</div>
<div id="wordpress_delete_confirm"<?php echo $wordpress_delete_confirm_css; ?>>
    Du bist dabei, deine WordPress-Seite zu l&ouml;schen. Bist du dir da ganz sicher?<br><br>

    <form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
        <input type="submit" name="<?php echo WordPress_Util::POST_ATTRIBUT_DELETE_SUBMIT_CONFIRM; ?>"
               value="Ja, WordPress-Seite l&ouml;schen">
    </form>
</div>