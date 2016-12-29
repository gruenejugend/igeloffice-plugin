<?php

/**
 * Created by PhpStorm.
 * User: KWM
 * Date: 28.12.2016
 * Time: 23:35
 */
class Domain_Front_Model extends User {
    public function __construct($id) {
        parent::__construct($id);
    }

    public function __get($name) {
        if (parent::__get($name)) {
            return parent::__get($name);
        }

        if($name == "isDomainPermitted") {
            return User_Control::isPermitted($this->ID, Domain_Front_Control::getDomainPermission()->id);
        } else if($name == "isWordPressPermitted") {
            return User_Control::isPermitted($this->ID, Domain_Front_Control::getWordPressPermission()->id);
        } else if($name == "domains") {
            $posts = get_posts(array(
                'post_type'                 => Domain_Util::POST_TYPE,
                'author'                    => $this->ID
            ));

            $domains = array();
            foreach ($posts AS $post) {
                $domains[] = new Domain($post->ID);
            }
            return $domains;
        } else if($name == "requestedDomains") {
            $posts = get_posts(array(
                'post_type'                 => Request_Util::POST_TYPE,
                'meta_query'                => array(
                    array(
                        'key'                       => Request_Util::ATTRIBUT_STELLER_IN,
                        'value'                     => $this->ID
                    ),
                    array(
                        'key'                       => Request_Util::ATTRIBUT_ART,
                        'value'                     => Request_Domain::art()
                    ),
                    array(
                        'key'                       => Request_Util::ATTRIBUT_STATUS,
                        'value'                     => "Angenommen",
                        'compare'                   => "!="
                    ),
                    array(
                        'key'                       => Request_Util::ATTRIBUT_STATUS,
                        'value'                     => "Abgelehnt",
                        'compare'                   => "!="
                    )
                )
            ));

            $requests = array();
            foreach ($posts AS $post) {
                $requests[] = new Request($post->ID);
            }

            return $requests;
        } else if($name == "requestedWordPress") {
            $posts = get_posts(array(
                'post_type'                 => Request_Util::POST_TYPE,
                'meta_query'                => array(
                    array(
                        'key'                       => Request_Util::ATTRIBUT_STELLER_IN,
                        'value'                     => $this->ID
                    ),
                    array(
                        'key'                       => Request_Util::ATTRIBUT_ART,
                        'value'                     => Request_WordPress::art()
                    ),
                    array(
                        'key'                       => Request_Util::ATTRIBUT_STATUS,
                        'value'                     => "Angenommen",
                        'compare'                   => "!="
                    ),
                    array(
                        'key'                       => Request_Util::ATTRIBUT_STATUS,
                        'value'                     => "Abgelehnt",
                        'compare'                   => "!="
                    )
                )
            ));

            $requests = array();
            foreach ($posts AS $post) {
                $requests[] = new Request($post->ID);
            }

            return $requests;
        } else if($name == "eigeneDomain") {
            return strtolower(str_replace(" ", "-", io_umlaute((new User(get_current_user_id()))->user_login))).".gruene-jugend.de";
        }
    }

}