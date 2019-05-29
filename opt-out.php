<?php
    // Globals
    $file = '/tmp/out.txt';

    function findmatch($string, $file) {
        global $matches;
        $matches = array();
        $handle = @fopen($file, "r");
        if ($handle)
        {
            while (!feof($handle))
            {
                $buffer = fgets($handle);
                if(strpos($buffer, $string) !== FALSE)
                    $matches[] = $buffer;
            }
            fclose($handle);
        }
    }

    function writeconfig($string, $file) {
        $current = file_get_contents($file);
        $current .= "$string";
        file_put_contents($file, $current);
    }

    function configure($dns, $mac, $dn) {
        $uuid = uniqid();
        $dns_base = "dhcp-option=tag:$uuid,6,";
        $sd_base = "dhcp-option=tag:$uuid,15,";
        $dhcp_base = "dhcp-host=$mac,set:$uuid";

        writeconfig("\n# $dn\n", $file);
        writeconfig("$dns_base . $dns", $file);
        writeconfig("$sd_base\n", $file);
        writeconfig($dhcp_base, $file);
    }

    if(isset($_POST['submit'])) {
        global $devicename, $macaddress, $dns;
        $devicename = $_POST['dn'];
        $macaddress = $_POST['mac'];
        $dns = $_POST['dns'];

        findmatch($macaddress, $file);
        if($matches) {
            echo "$matches[0]";
        } else {
            echo "Match not found!";
            configure($dns, $macaddress, $devicename);
        }
    }
?>