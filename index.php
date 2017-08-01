<?php
require_once 'vendor/autoload.php';
$client = new \Goutte\Client();
$crawler = $client->request("GET", "https://www.fio.cz/akcie-investice/dalsi-sluzby-fio/devizove-konverze");
$findCurrencies = ['EUR', 'USD'];

$rates = array_filter($crawler->filterXPath('//table[@class="tbl-sazby"]/tbody/tr')->each(function($node) use ($findCurrencies) {
    foreach ($findCurrencies as $findCurrency) {
        if ($node->filterXPath("//td")->text() == $findCurrency) {
            return [$findCurrency => [
                'nakup' => (float) str_replace(",", ".", $node->filterXPath("//td[4]")->text()),
                'prodej' => (float) str_replace(",", ".", $node->filterXPath("//td[5]")->text())
            ]];
        }
    }
}), function ($value) {
    return $value != null;
});

$cleanRates = [];
foreach ($rates as $rate) {
    foreach ($rate as $currency => $cleanRate) {
        $cleanRates[$currency] = $cleanRate;
    }
}

echo json_encode($cleanRates);