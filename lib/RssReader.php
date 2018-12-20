<?php
class RssReader
{
	var $xml_doc;
	public function __construct()
	{
		$this->xml_doc = new DOMDocument();
	}

	public function getFeed($xml)
	{
		$this->xml_doc->load($xml);
		$channel=$this->xml_doc->getElementsByTagName('channel')->item(0);
		$channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
		$channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
		$channel_desc = $channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
		echo("<p><a href='" . $channel_link. "'>" . $channel_title . "</a>");
		echo("<br />");
		echo($channel_desc . "</p>");
		$x=$this->xml_doc->getElementsByTagName('item');
		for ($i=0; $i<=2; $i++)
		{
			$item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
			$item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
			$item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
			echo ("<p><a href='" . $item_link. "'>" . $item_title . "</a>");
			echo ("<br />");
			echo ($item_desc . "</p>");
		}
	}
}
/*
//$xml=("http://news.google.com/news?ned=us&topic=h&output=rss");
//$xml=("http://rss.msnbc.msn.com/id/3032091/device/rss/rss.xml");
//$xml=("http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml");
//$xml=("http://platsbanken.arbetsformedlingen.se/Rss/RssFeed.aspx?q=s(t(php))a(20)sp(67)sr(1)c(1AD33D9E)");
$rss_reader = new RssReader();
$rss_reader->getFeed($xml);
*/
?>