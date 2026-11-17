<?php
$json = json_decode(file_get_contents('/home/sim/www/mooblipoisid/docs/kadence-theme-mods.json'), true);

$mods = array();
$i = 0;
while ($i < count($json)) {
    $item = $json[$i];
    $key = $item['key'];
    $value = $item['value'];
    
    if ($key === '=>' || $value === '=>') {
        $i++;
        continue;
    }
    
    if (strpos($key, '    ') === 0) {
        $i++;
        continue;
    }
    
    $nested = array();
    $j = $i + 1;
    while ($j < count($json) && strpos($json[$j]['key'], '    ') === 0) {
        $nkey = trim($json[$j]['key']);
        $nval = $json[$j]['value'];
        if ($nval !== '=>') {
            $nested[$nkey] = $nval;
        }
        $j++;
    }
    
    if (!empty($nested)) {
        $mods[$key] = $nested;
    } else {
        $mods[$key] = $value;
    }
    $i = $j > $i + 1 ? $j : $i + 1;
}

update_option('theme_mods_mooblipoisid-child', $mods);
echo "Applied " . count($mods) . " theme mods\n";
