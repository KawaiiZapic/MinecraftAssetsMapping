<?php
// Read Args.
define("USAGE_TEXT","Usage: " . $argv[0] . " <AssetsJsonFile> <AssetsDir> <OutputDir>");
if($argc < 4) {
    die("[ERROR] Invalid arguments num.".PHP_EOL.USAGE_TEXT.PHP_EOL);
}
$MappingFile = $argv[1];
$AssetsDir = $argv[2];
$OutputDir = $argv[3];

// Read JSON file.
print_r("[INFO] Reading mapping file at {$MappingFile}...".PHP_EOL);
if(!file_exists($MappingFile)){
    die("[ERROR] The mapping file \"{$MappingFile}\" not exists!".PHP_EOL);
}
$MappingData = file_get_contents($MappingFile);
if(!$MappingData) {
    die("[ERROR] Failed to read mapping file \"{$MappingFile}\",check if there is any WARNING above!".PHP_EOL);
}
$MappingData = json_decode($MappingData,true);
if(!$MappingData) {
    die("[ERROR] Failed to read mapping file \"{$MappingFile}\",check if there is any WARNING above!".PHP_EOL);
}
if(!isset($MappingData['objects']) || count($MappingData['objects']) == 0) {
    die("[ERROR] The mapping file \"{$MappingFile}\" is invalid!".PHP_EOL);
}
print_r("[INFO] Get " . count($MappingData['objects']) . " files to map.".PHP_EOL);

// Check AssetsDir
if(!file_exists($AssetsDir)) {
    die("[ERROR] The assets directory \"{$AssetsDir}\" not exists!".PHP_EOL);
}

// Check OutputDir
if(!file_exists($OutputDir)) {
    print_r("[WARN] The output directory \"{$OutputDir}\" is not exists,creating...".PHP_EOL);
    if(!mkdir($OutputDir,0644,true)){
        die("[ERROR] Failed to create output directory \"{$OutputDir}\",check if there is any WARNING above!".PHP_EOL);
    }
} elseif (count(scandir($OutputDir)) > 2) {
    die("[ERROR] The output directory \"{$OutputDir}\" is not empty!".PHP_EOL);
}

//Main
$MappingData = $MappingData['objects'];
foreach($MappingData as $path => $data) {
    if(!isset($data['hash'])){
        print_r("[WARN] File \"{$path}\" does not has a hash,skipping...".PHP_EOL);
        continue;
    }
    $hash = $data['hash'];
    $hashPre = substr($hash,0,2);
    if(!file_exists("{$AssetsDir}/{$hashPre}/{$hash}")) {
        print_r("[WARN] Hash \"{$hash}\" is not exists,skipping...".PHP_EOL);
        continue;
    }
    $target = "{$OutputDir}/{$path}";
    print_r("{$AssetsDir}/{$hashPre}/{$hash} -> {$target}".PHP_EOL);
    if(!file_exists(dirname($target))) {
        mkdir(dirname($target), 0777, true);
    }
    if(!copy("{$AssetsDir}/{$hashPre}/{$hash}",$target)){
        print_r("[WARN] Some error in copy \"{$AssetsDir}/{$hashPre}/{$hash}\" to \"$target\",check if there is any WARNING above.".PHP_EOL);
    }
}