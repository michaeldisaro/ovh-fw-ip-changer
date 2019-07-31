# ovh-fw-ip-changer
A simple script to run if you have dynamic IP address to change the old ip in the "ssh access rule" on your OVH firewall 
with the new one.

**SETUP**

Clone this repository and run a ***composer install*** command.

Create an OVH API application and authorize it following: https://docs.ovh.com/gb/en/customer/first-steps-with-ovh-api/

Once you have APPLICATION KEY, APPLICATION SECRET and CUSTOMER KEY create a ***config.php*** using the following template:
```
<?php
$config = [
    "AK" => "1mXkn5umKZUY5bK6",
    "AS" => "XV5RM7pDt3nGU49te1kEAp08UiKBQP3K",
    "CK" => "A7y4fpDc0HKNDPo7jrJnpvYx7TX9OYzU",
    "priority" => 1
];
```

AK = APPLICATION KEY, AS = APPLICATION SECRET, CK = CUSTOMER KEY and priority = ssh rule's priority in your FW chain.

**RUN**

Just run ***php ip-changer.php***

**WARNING**

DO NOT LEAVE YOUR CREDENTIALS AROUND! WHO HAS THEM CAN ALLOW SSH TO YOUR MACHINE FOR HIS IP!