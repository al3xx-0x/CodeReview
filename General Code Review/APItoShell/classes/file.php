<?php

class File
{
    public $owner;
    public $uuid;
    public $content;
    public $logfile = "/var/www/logs/application.log";

    function __construct($id, $owner, $uuid)
    {
        $this->id     = $id;
        $this->owner  = $owner;
        $this->uuid   = $uuid;

        echo "/var/www/data/" . $uuid;
        $this->content = file_get_contents("/var/www/data/" . $uuid);
    }

    function __destruct()
    {
        // Logging access
        $fd = fopen($this->logfile, 'a');
        fwrite(
            $fd,
            $_GET['action'] . ":" . $this->uuid . ' by ' . $this->owner . "\n"
        );
        fclose($fd);
    }

    public static function index($user_id)
    {
        $sql = "SELECT * FROM files WHERE user_id=" . intval($user_id);
        $results = mysql_query($sql);

        $files = [];

        if ($results) {
            while ($row = mysql_fetch_assoc($results)) {
                $files[] = [
                    'id'   => $row['id'],
                    'name' => $row['name'],
                    'uuid' => $row['uuid'],
                    'sig'  => sign(intval($user_id) . ':' . $row['uuid'])
                ];
            }
        }

        return $files;
    }

    public static function get_file($user_id, $uuid, $sig)
    {
        // Verify signature (VULNERABLE: loose comparison)
        if ($sig != sign($user_id . ':' . $uuid)) {
            respond_with(["error" => "Invalid Signature"]);
        } else {

            // Retrieve file owner
            $sql = "SELECT * FROM users WHERE id=" . intval($user_id);
            $result = mysql_query($sql);

            if ($result) {
                $row = mysql_fetch_assoc($result);
                $file = new File($user_id, $row['login'], $uuid);
                respond_with(["file" => $file]);
            }

            respond_with(["error" => "file not found"]);
        }
    }

    public static function upload($name, $data, $user_id)
    {
        $uuid = uniqid("file") . uniqid();

        $sql  = "INSERT INTO files (name, uuid, user_id) VALUES ('";
        $sql .= mysql_real_escape_string($name);
        $sql .= "','";
        $sql .= mysql_real_escape_string($uuid);
        $sql .= "'," . intval($user_id) . ")";

        mysql_query($sql);

        // Write file to disk
        $fd = fopen("/var/www/data/" . $uuid, 'w');
        fwrite($fd, $data);
        fclose($fd);

        respond_with(["success" => "File successfully uploaded"]);
    }
}

?>
