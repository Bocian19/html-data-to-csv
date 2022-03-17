<?php

namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;



class HtmlToCsvCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:parse-html';

    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Parsing data from html file to csv file';

    protected function configure(): void
    {
        // ...
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $html = file_get_contents("public/wo_for_parse.html");
        $converter = new Crawler($html);

        $tracking_number = $converter->filter('#wo_number')->text();
        $po_number = $converter->filter('#po_number')->text();
        $scheduled_date = date("Y-m-d H:i", strtotime($converter->filter('#scheduled_date')->text()));
        $customer = $converter->filter('#customer')->text();
        $trade = $converter->filter('#trade')->text();
        $nte = floatval(preg_replace("/[^-0-9\.]/","",$converter->filter('#nte')->text()));
        $store_id = $converter->filter('#location_name')->text();
        $address = $converter->filter('#location_address')->text();
        $phone_number = $converter->filter('#location_phone')->text();

        $data = [['Tracking-number', $tracking_number], ['PO-number', $po_number], ['Scheduled', $scheduled_date], ['Customer', $customer], ['Trade', $trade], ['NTE', $nte], ['Store-ID', $store_id], ['Address', $address], ['Phone-number', $phone_number]];

        $fp = fopen('public/file.csv', 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields, ':', ' ');
        }
        fclose($fp);

        return Command::SUCCESS;

    }
}