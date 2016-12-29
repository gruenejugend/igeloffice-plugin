<?php
/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 29.12.2016
 * Time: 11:27
 */

function domain_zeile($id, $host, $status, $first = false, $request = true, $ziel = "", $typ = "") {
    ?>
    <tr>
        <td>
            <?php echo $host; ?>
            <input
                type="hidden"
                name="<?php echo Domain_Front_View::POST_DOMAIN.$id; ?>"
                value="<?php echo $host; ?>">
        </td>
        <td><?php echo $status; ?></td>
        <td>
            <?php if($request) { ?>
            <select
                id="<?php echo Domain_Front_View::POST_ZWECK.$id; ?>"
                name="<?php echo Domain_Front_View::POST_ZWECK.$id; ?>"
                size="1">
                <?php
                if($first) {
                    ?>                  <option value="0"></option>
<?php
                }

                foreach(Domain_Front_View::POST_ZWECKE AS $key => $label) {
                    if($key != Domain_Util::VZ_WORDPRESS || (new Domain_Front_Model(get_current_user_id()))->isWordPressPermitted) {
                        ?>
                        <option value="<?php echo $key; ?>">
                            <?php echo $label; ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
            <?php } else {
                echo $typ;
            } ?>
        </td>
        <td>
            <div style="display: none;" id="<?php echo Domain_Front_View::DIV_TARGET_ZIEL.$id; ?>">
                Weiterleitung zu:<br>
                <?php if($request) { ?>
                <input type="url" name="<?php echo Domain_Front_View::POST_REDIRECT.$id; ?>" value="">
                <?php } else {
                    echo $ziel;
                } ?>
            </div>
            <div id="<?php echo Domain_Front_View::DIV_TARGET_LINK.$id; ?>">
                <?php

                if($id != 0) {
                    echo "WordPress";
                }

                ?>
            </div>
        </td>
    </tr>
    <?php
}

?>

<h1>Internetadresse</h1>

&Uuml;ber das IGELoffice besteht die M&ouml;glichkeit, Internetadressen einzurichten und ihre Nutzung zu bestimmten. Bei den sogenannten &quot;Subdomains&quot; handelt es sich um den Teil, der vor dem &quot;gruene-jugend.de&quot; steht.<br><br>

Basisgruppen und Landesverb&auml;nde haben so die M&ouml;glichkeit, Internetadressen zu erstellen, mit denen sie leichter im Internet zu finden sind.<br><br>

Der Subdomain kann eine Adresse hinterlegt werden, zu der die Subdomain f&uuml;hren soll. Es besteht aber auch die M&ouml;glichkeit mit WordPress eine Homepage zu erstellen, zu der die Subdomain dann f&uuml;hrt.<br><br>


<h1>Eigene Homepage - WordPress</h1>

Für Basisgruppen und Landesverbände sind Homepages besonders wichtig, da sie die Präsenz der jeweiligen Gruppe im Internet darstellen. Über diese Angebote präsentiert sich die Gruppe nach aussen und bietet Interessierten die Möglichkeit zur Information.<br><br>

Zur Verwaltung der Homepage nutzen wir WordPress. WordPress ist ein System zur Verwaltung von Homepages. Es bietet euch die Möglichkeit, eure Homepage zu aktualisieren und Inhalte auf der Homepage einzustellen.<br><br>

Da die Installation von WordPress-Seiten technisches Know-How voraussetzt und oft auch mit Kosten verbunden ist bietet euch das IGELoffice die Möglichkeit, einfach, unkompliziert und kostenlos eine WordPress-Homepage zu erstellen.<br><br>

<h1>Deine Adressen</h1>

<form action="<?php echo($_SERVER["REQUEST_URI"]); ?>" method="post">
    <?php

    wp_nonce_field(self::NONCE, self::POST_NONCE);

    ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <td>Adresse</td>
        <td>Status</td>
        <td>Verwendung</td>
        <td>Ziel</td>
    </thead>
    <?php

        $model = new Domain_Front_Model(get_current_user_id());

        //Eigene, noch nicht erstellte Domains
        $eigeneDomain = $model->eigeneDomain;
        if(!MySQL_Proxy::checkHostExists($eigeneDomain)) {
            domain_zeile(0, $eigeneDomain, "Noch nicht erstellt, frei", true);
        }

        //Domains, die bereits erstellt wurden
        foreach($model->domains AS $domain) {
            domain_zeile($domain->id, $domain->host, "Aktiv");
        }

        //Domain, die beantragt wurden
        foreach($model->requestedDomains AS $request) {
            domain_zeile("r".$request->id,$request->meta[Request_Util::DETAIL_DOMAIN_HOST],"Beantragt", false, false, $request->meta[Request_Util::DETAIL_DOMAIN_TARGET],"Weiterleitung");
        }

        //WordPress, die beantragt wurden
        foreach($model->requestedWordPress AS $request) {
            domain_zeile("r".$request->id,$request->meta[Request_Util::DETAIL_DOMAIN_HOST],"Beantragt", false, false, "WordPress","WordPress");
        }

    ?>
</table>
<input type="submit" name="<?php echo Domain_Front_View::POST_SUBMIT; ?>" value="Abschicken">
</form>

<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        var viewDiv = function(id) {
            if($("#<?php echo Domain_Front_View::POST_ZWECK; ?>"+id).val()
                == "<?php echo Domain_Util::VZ_REDIRECT; ?>") {
                $("#<?php echo Domain_Front_View::DIV_TARGET_ZIEL; ?>"+id).show();
                $("#<?php echo Domain_Front_View::DIV_TARGET_LINK; ?>"+id).hide();
            } else {
                $("#<?php echo Domain_Front_View::DIV_TARGET_ZIEL; ?>"+id).hide();
                $("#<?php echo Domain_Front_View::DIV_TARGET_LINK; ?>"+id).show();
            }
        };

        <?php
        if(!MySQL_Proxy::checkHostExists($eigeneDomain)) {
            ?>
        $("#<?php echo Domain_Front_View::POST_ZWECK; ?>0").change(function () {
            viewDiv(0);
        });
        <?php
        }

        foreach($model->domains AS $domain) {
        ?>
        $("#<?php echo Domain_Front_View::POST_ZWECK.$domain->id; ?>").change(function () {
            viewDiv(<?php echo $domain->id; ?>);
        });
        <?php
        }
        ?>
    });
</script>