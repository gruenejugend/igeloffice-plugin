<?php

/**
 * Description of Log_Control
 *
 * @author KWM
 */
class Log_Control
{
    private $res = null;
    private $datum = null;

    private function __construct()
    {
        $this->datum = getdate();

        $pfad = "../../log/";
        if (is_admin()) {
            $pfad = "../" . $pfad;
        }

        $this->res = fopen($pfad . "igeloffice_" . $this->datum['mon'] . "_" . $this->datum['year'] . ".log", "a");
    }

    public static function writeLog($datei, $message)
    {
        $log_control = new Log_Control();
        $log_control->write($datei, $message);
        unset($log_control);
    }

    private function write($datei, $message)
    {
        $write = $this->datum['year'] . "-" . $this->datum['mon'] . "-" . $this->datum['mday'] . " " . $this->datum['hours'] . ":" . $this->datum['minutes'] . ":" . $this->datum['seconds'] . " in " . $datei . ": " . $message;
        fwrite($this->res, $write);
    }

    private function __destruct()
    {
        fclose($this->res);
    }
}