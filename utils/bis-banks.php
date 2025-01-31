<?php

$url = 'http://www.bis.org/cbanks.htm';
$data = file_get_contents($url);

# cut data before table
$data = preg_replace('/^.*<div class="country_institutions">/ms','',$data);

# cut data after table
$data = preg_replace('/<\/tbody>.*$/ms','',$data);

# split in to rows
preg_match_all('/<tr>.*?<td valign="top"><a id="country_([A-Z]{2})" name="country_.." href="[^"]+">([^<>]+)<\/a><\/td>.*?<td valign="top"><a class="external" target="_blank" href="\/dcms\/goto.jsp\?([^"]+)">([^<>]+)<\/a><\/td>.*?<\/tr>/s',$data,$matches);

# remove whole-row capture
array_shift($matches);

# display results
for($i=0;$i<count($matches[0]);$i++) {
 print $matches[0][$i] . '|' . $matches[1][$i] . '|' . $matches[2][$i] . '|' . $matches[3][$i] . "\n";
}
