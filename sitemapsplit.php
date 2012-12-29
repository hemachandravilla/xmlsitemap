<?php
//function sitemapsplit(){

  // get contents of a gz-file into a string
	$filename = "sitemap-3.xml.gz";
	$zd = gzopen($filename, "r");
	$contents = gzread($zd, 50000000);

	$file_count = 1;
	$url_count = 0;
	$url_max_count = 5000;

	$last_file_idex = 0;
	$index = 0;
	$root_path = "http://" .$_SERVER["SERVER_NAME"] . "/";

	$url_start = "<url>";
	$url_end = "</url>";
	$urlset_end = "\n</urlset>";
	$sitemap_index_header = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	$sitemap_index_end = "\n</sitemapindex>";

	$index_file = fopen("sitemap_index.xml" , "w");

	fwrite($index_file , $sitemap_index_header);

	$xml_header = substr($contents , 0, strpos($contents , $url_start,0));
	$index = strpos($contents , $url_start,0);

	$sitemap_xml  = $xml_header;
	$file = fopen("sitemap".$file_count.".xml" , "wb");
	fwrite($file , $sitemap_xml);

	while($index < strlen($contents)){
		$pos = strpos($contents , $url_end,$index+1);

		if($pos != false){
			$url_count++ ;
			fwrite($file , substr($contents , $index, $pos - $index + strlen($url_end) ));

		}else{
			break;
		}

		$index = $pos+ strlen($url_end);

		if($url_count >= $url_max_count){

			fwrite($file ,$urlset_end);
			fclose($file);

			$file = fopen("sitemap".$file_count.".xml" , "rb");
			$zp = gzopen("sitemap". $file_count .".xml.gz", "w9");

			while(!feof($file)){
				gzwrite($zp, fgets($file));
			}

			gzclose($zp);
			fclose($file);
			unlink("sitemap".$file_count.".xml");

			// Add sitemap to sitemap_index.xml
			$index_str = "<sitemap> \n<loc>". $root_path . "sitemap". $file_count .".xml.gz</loc>\n";
			$index_str .= "<lastmod>". date(DATE_ATOM) . "</lastmod>\n</sitemap>\n";
			fwrite($index_file , $index_str);

			$file_count++;
			$url_count = 0;

			$file = fopen("sitemap".$file_count.".xml" , "wb");
			fwrite($file , $xml_header);

		}


	}

	fwrite($file ,$urlset_end);
	fclose($file);

	$file = fopen("sitemap".$file_count.".xml" , "rb");
	$zp = gzopen("sitemap". $file_count .".xml.gz", "w9");

	while(!feof($file)){
		gzwrite($zp, fgets($file));
	}

	gzclose($zp);
	fclose($file);
	unlink("sitemap".$file_count.".xml");
	// Add sitemap to sitemap_index.xml
	$index_str = "<sitemap> \n<loc>". $root_path . "sitemap". $file_count .".xml.gz</loc>\n";
	$index_str .= "<lastmod>". date(DATE_ATOM) . "</lastmod>\n</sitemap>";
	fwrite($index_file , $index_str);
	fwrite($index_file , $sitemap_index_end);
	fclose($index_file);
//}

?>
