<?php
require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/config.php';

use \Ovh\Api;

// Get my current IP
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.myip.com/");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$myIP = json_decode(curl_exec($ch));
curl_close($ch);

$conn = new Api($config['AK'], $config['AS'], 'ovh-eu', $config['CK']);
$ips = $conn->get('/ip');
foreach ($ips as $ipBlock) {
    if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ipBlock)) {
        $url = '/ip/' . urlencode($ipBlock) . '/firewall';
        $ipUnderFirewall = $conn->get($url, ['enabled' => true])[0];

        echo PHP_EOL. "MANAGING $ipUnderFirewall FIREWALL'S SSH RULE" . PHP_EOL;

        $url .= '/' . $ipUnderFirewall . '/rule';

        // Delete old rule
        echo "Deleting old ssh rule..." . PHP_EOL;
        $isPending = true;
        try {
            $result = $conn->delete($url . "/{$config['priority']}");
        } catch (\Exception $ex) {
            // Usually this goes in error when no rule is present
            // Uncomment if weird problems occur during deletion of an existing rule
            //echo $ex->getMessage();
            $isPending = false;
        }

        // Wait for pending deletion
        while ($isPending) {
            sleep(5);
            $isPending = @$conn->get($url, ['state' => 'removalPending'])[0] == $config['priority'] ?? false;
            echo ($isPending ? "Waiting for deletion..." : "Rule 1 has been deleted") . PHP_EOL;
        }
        echo "Adding new rule for ip {$myIP->ip}" . PHP_EOL;

        // Add new rule
        try {
            $result = $conn->post($url, [
                'action' => 'permit',
                'destinationPort' => '22',
                'protocol' => 'tcp',
                'sequence' => $config['priority'],
                'source' => $myIP->ip . '/32'
            ]);
            echo "NEW Rule 1 is pending creation..." . PHP_EOL;
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
