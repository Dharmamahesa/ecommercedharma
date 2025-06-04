<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rss {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Ambil dan parsing RSS feed dari URL tertentu
     *
     * @param string $url URL RSS feed
     * @param int $limit Batas jumlah item yang ditampilkan
     * @return array|null
     */
    public function get_feed($url, $limit = 5)
    {
        $rssData = @file_get_contents($url);
        if ($rssData === FALSE) {
            return null;
        }

        $xml = @simplexml_load_string($rssData);
        if ($xml === FALSE || !isset($xml->channel->item)) {
            return null;
        }

        $items = [];
        $count = 0;
        foreach ($xml->channel->item as $item) {
            if ($count >= $limit) break;
            $items[] = [
                'title'       => (string) $item->title,
                'link'        => (string) $item->link,
                'description' => (string) $item->description,
                'pubDate'     => (string) $item->pubDate
            ];
            $count++;
        }

        return $items;
    }
}
